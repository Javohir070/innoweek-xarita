<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;
use ZipArchive;

/**
 * Ko'rgazma Excel faylining "Рўйхат" varag'ini o'qiydi.
 *
 * Ustunlar: A т/р, B yo'nalish, C павильон (stend), D жой, E tashkilot, F ishlanma haqida, G mas'ul shaxs;
 * H, J, L, ... — 1..10-mahsulot nomi va rasmi (rasm nom katagida yoki o'ngidagi katakda turadi).
 * Eski ko'rinishdagi faylda H — mas'ul boshqarma bo'lib, mahsulotlar I ustunidan boshlanadi (sarlavhadan aniqlanadi).
 *
 * "Жой" katagida oraliq bo'lishi mumkin ("1-2", "18-20"): birinchi joy — asosiy, qolganlari uning davomi ("cont").
 * "Жой" katagi bir nechta qatorga birlashtirilgan bo'lsa, shu qatorlardagi tashkilotlar oraliqni o'zaro bo'lishadi.
 */
class ExcelExpoReader
{
    private const FIRST_ROW = 4;

    private const HEADER_ROW = 3;

    private const PRODUCT_SLOTS = 10;

    /** @var array<string, array{int,int}> "r:c" -> birlashtirilgan hududning yuqori-chap katagi */
    private array $top = [];

    private Worksheet $ws;

    /** Birinchi mahsulot ustuni: 8 (H) yoki eski faylda 9 (I) */
    private int $productCol = 8;

    /** @var string[] */
    private array $warnings = [];

    public function __construct(private ExpoImageStore $images) {}

    /** Oxirgi o'qishda o'tkazib yuborilgan qatorlar haqida ogohlantirishlar */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /** @return array<int, array> ExpoImporter::replaceAll uchun qatorlar */
    public function read(string $file, ?callable $progress = null): array
    {
        $book = IOFactory::createReader('Xlsx')->load($file);
        $this->ws = $this->findListSheet($book->getAllSheets());
        $this->warnings = [];
        $this->indexMerges();
        // H sarlavhasi "... расми" bo'lsa mahsulotlar H dan boshlanadi, aks holda H — mas'ul boshqarma
        $hasDept = mb_stripos($this->val(self::HEADER_ROW, 8), 'расм') === false;
        $this->productCol = $hasDept ? 9 : 8;
        $images = $this->extractImages($file, $progress);

        $blocks = [];   // bitta "Жой" katagiga tegishli tashkilotlar
        $entries = [];
        $last = $this->ws->getHighestDataRow();
        for ($r = self::FIRST_ROW; $r <= $last; $r++) {
            $stand = $this->normStand($this->val($r, 3));
            if ($stand === '') {
                continue;
            }

            $products = [];
            for ($slot = 0; $slot < self::PRODUCT_SLOTS; $slot++) {
                $c = $this->productCol + 2 * $slot;
                // birlashtirilgan katakning davomidagi nom takrorlanmasin
                $name = $this->isTopLeft($r, $c) ? $this->text($r, $c) : '';
                $files = array_values(array_unique($images["$r:$slot"] ?? []));
                if ($name !== '' || $files) {
                    $products[] = ['name' => $name, 'img' => $files];
                }
            }
            // nomlar yozilmagan bo'lsa, qatordagi barcha rasmlar bitta ishlanmaniki
            if (count($products) > 1 && ! array_filter(array_column($products, 'name'))) {
                $products = [['name' => '', 'img' => array_values(array_unique(array_merge(...array_column($products, 'img'))))]];
            }

            [$org, $info, $contact] = [ltrim($this->text($r, 5), ', '), $this->text($r, 6), $this->text($r, 7)];
            $dept = $hasDept ? $this->text($r, 8) : '';
            $cont = null;
            $topRow = $this->topOf($r, 5)[0];
            if ($org !== '' && $topRow < $r) {
                $cont = (int) $this->val($topRow, 4); // tashkilot nomi katagi yuqoridan birlashtirilgan
            }
            if ($org === '' && $this->continuesFromAbove($r)) {
                // mahsulot kataklari yuqoridan davom etadi — shu stenddagi oldingi tashkilot
                foreach (array_reverse($entries) as $prev) {
                    if ($prev['stand'] === $stand && $prev['org'] !== '') {
                        [$org, $info, $contact, $dept, $cont] = [$prev['org'], $prev['info'], $prev['contact'], $prev['dept'], $prev['first']];
                        break;
                    }
                }
            }
            if ($org === '' && $info !== '') {
                // tashkilot yozilmagan, faqat loyiha nomi bor — joy band ko'rinishi uchun nom sifatida olinadi
                [$org, $info] = [$info, str_contains($info, "\n") ? $info : ''];
            }

            $key = $stand.':'.$this->topOf($r, 4)[0];
            if (! isset($blocks[$key])) {
                $places = $this->parsePlaces($this->val($r, 4));
                if (! $places) {
                    $this->warnings[] = "$r-qator: \"Жой\" katagi tushunarsiz («{$this->val($r, 4)}») — o'tkazib yuborildi.";

                    continue;
                }
                $blocks[$key] = ['places' => $places, 'entries' => []];
            }
            $entry = [
                'row' => $r,
                'stand' => $stand,
                'first' => $blocks[$key]['places'][0],
                'cont' => $cont,
                'section' => $this->text($r, 2),
                'org' => $org,
                'info' => $info,
                'contact' => $contact,
                'dept' => $dept,
                'products' => $products,
            ];
            $blocks[$key]['entries'][] = $entry;
            $entries[] = $entry;
        }

        return $this->expand($blocks);
    }

    /** "5" -> [5], "1-3" -> [1, 2, 3] */
    private function parsePlaces(string $s): array
    {
        if (! preg_match('/^(\d+)(?:\s*[-–—]\s*(\d+))?$/u', $s, $m)) {
            return [];
        }
        $from = (int) $m[1];
        $to = (int) ($m[2] ?? $from);

        return $to >= $from && $to - $from < 100 ? range($from, $to) : [];
    }

    /** Bloklarni joylar bo'yicha yoyadi: har bir joyga bitta qator */
    private function expand(array $blocks): array
    {
        $stands = config('expo.stands');
        $max = config('expo.places_per_stand');
        $rows = [];
        $seen = [];
        foreach ($blocks as $block) {
            $places = $block['places'];
            $entries = $block['entries'];
            // tashkilotlar joylardan ko'p bo'lsa, ortiqchalari oxirgi joyga qo'shiladi
            while (count($entries) > count($places)) {
                $extra = array_pop($entries);
                $i = count($entries) - 1;
                foreach (['org', 'info', 'contact', 'dept'] as $k) {
                    $entries[$i][$k] = trim($entries[$i][$k]."\n".$extra[$k]);
                }
                $entries[$i]['products'] = array_merge($entries[$i]['products'], $extra['products']);
            }

            $base = intdiv(count($places), count($entries));
            $rest = count($places) % count($entries);
            foreach ($entries as $i => $e) {
                $own = array_splice($places, 0, $base + ($i < $rest ? 1 : 0));
                foreach ($own as $j => $number) {
                    $where = "{$e['row']}-qator ({$e['stand']}-$number)";
                    if (! in_array($e['stand'], $stands, true) || $number < 1 || $number > $max) {
                        $this->warnings[] = "$where: bunday stend yoki joy yo'q — o'tkazib yuborildi.";

                        continue;
                    }
                    if (isset($seen[$e['stand'].':'.$number])) {
                        $this->warnings[] = "$where: bu joy {$seen[$e['stand'].':'.$number]}-qatorda band — o'tkazib yuborildi.";

                        continue;
                    }
                    $seen[$e['stand'].':'.$number] = $e['row'];
                    $main = $j === 0;
                    $rows[] = [
                        'stand' => $e['stand'],
                        'place' => $number,
                        'cont' => $main ? $e['cont'] : $own[0],
                        'section' => $e['section'],
                        'org' => $e['org'],
                        'info' => $main ? $e['info'] : '',
                        'contact' => $main ? $e['contact'] : '',
                        'dept' => $main ? $e['dept'] : '',
                        'products' => $main ? $e['products'] : [],
                    ];
                }
            }
        }

        return $rows;
    }

    /** @param Worksheet[] $sheets */
    private function findListSheet(array $sheets): Worksheet
    {
        foreach ($sheets as $s) {
            // sarlavhada "Павильон" ustuni bor varaq
            if (trim((string) $s->getCell('C3')->getValue()) === 'Павильон') {
                return $s;
            }
        }
        throw new RuntimeException("Excel'da ro'yxat varag'i topilmadi (C3 katakda \"Павильон\" bo'lishi kerak).");
    }

    private function indexMerges(): void
    {
        $this->top = [];
        foreach ($this->ws->getMergeCells() as $range) {
            [[$c1, $r1], [$c2, $r2]] = Coordinate::rangeBoundaries($range);
            for ($r = $r1; $r <= $r2; $r++) {
                for ($c = $c1; $c <= $c2; $c++) {
                    $this->top["$r:$c"] = [$r1, $c1];
                }
            }
        }
    }

    /** @return array{int,int} */
    private function topOf(int $r, int $c): array
    {
        return $this->top["$r:$c"] ?? [$r, $c];
    }

    private function isTopLeft(int $r, int $c): bool
    {
        return $this->topOf($r, $c) === [$r, $c];
    }

    private function continuesFromAbove(int $r): bool
    {
        for ($c = $this->productCol; $c < $this->productCol + 2 * self::PRODUCT_SLOTS; $c++) {
            if ($this->topOf($r, $c)[0] < $r) {
                return true;
            }
        }

        return false;
    }

    /** Katak qiymati (birlashtirilgan bo'lsa — yuqori-chap katakniki) */
    private function val(int $r, int $c): string
    {
        [$r0, $c0] = $this->topOf($r, $c);
        $v = $this->ws->getCell([$c0, $r0])->getValue();
        if ($v instanceof RichText) {
            $v = $v->getPlainText();
        }

        return trim(str_replace("\r\n", "\n", (string) $v));
    }

    /** Matnli katak: "-" (ma'lumot yo'q belgisi) bo'sh hisoblanadi */
    private function text(int $r, int $c): string
    {
        $v = $this->val($r, $c);

        return $v === '-' ? '' : $v;
    }

    private function normStand(string $s): string
    {
        // kirillcha А/В -> lotincha A/B
        return strtr(mb_strtoupper(trim($s)), ['А' => 'A', 'В' => 'B']);
    }

    /**
     * Rasmlarni diskka saqlaydi.
     *
     * @return array<string, string[]> "qator:mahsulot_slot" -> rasm yo'llari
     */
    private function extractImages(string $file, ?callable $progress): array
    {
        $zip = new ZipArchive;
        if ($zip->open($file) !== true) {
            throw new RuntimeException('Excel faylini ochib bo\'lmadi.');
        }

        // [media fayl, ustun, qator]
        $pictures = [];
        foreach ($this->ws->getDrawingCollection() as $d) {
            if ($d instanceof Drawing) {
                $media = str_contains($d->getPath(), '#') ? substr($d->getPath(), strpos($d->getPath(), '#') + 1) : $d->getPath();
                [$col, $row] = Coordinate::indexesFromString($d->getCoordinates());
                // rasm katakning eng pastidan boshlanib, asosan keyingi qatorda tursa — o'sha qatorniki
                $rowPx = $this->ws->getRowDimension($row)->getRowHeight() * 96 / 72;
                $inRow = $rowPx - $d->getOffsetY();
                if ($rowPx > 0 && $d->getHeight() > 2 * max(0, $inRow)) {
                    $row++;
                }
                $pictures[] = [ltrim($media, '/'), $col, $row];
            }
        }
        array_push($pictures, ...$this->groupedPictures($zip));

        $saved = [];   // media fayl -> saqlangan yo'l (bir rasm bir necha joyda bo'lsa bir marta saqlanadi)
        $result = [];
        foreach ($pictures as [$media, $col, $row]) {
            if (! array_key_exists($media, $saved)) {
                $bin = $zip->getFromName($media);
                // brauzer ko'rsata olmaydigan rasm (masalan, EMF) null bo'lib qoladi
                $saved[$media] = $bin === false ? null
                    : $this->images->putBinary($bin, 'x'.pathinfo($media, PATHINFO_FILENAME), strtolower(pathinfo($media, PATHINFO_EXTENSION)));
                $progress && $progress();
            }
            if ($saved[$media] === null) {
                continue;
            }

            $row = $this->topOf($row, $col)[0];
            // nom katagi va o'ngidagi katak bitta mahsulotniki: H,I -> 0; J,K -> 1; ...
            $slot = min(self::PRODUCT_SLOTS - 1, max(0, intdiv($col - $this->productCol, 2)));
            $result["$row:$slot"][] = $saved[$media];
        }
        $zip->close();

        return $result;
    }

    /**
     * PhpSpreadsheet guruhlangan rasmlarni (Excel'da "Группировать") o'qimaydi — ularni varaqning drawing XML'idan olamiz.
     * Guruhdan birinchi rasm olinadi.
     *
     * @return array<int, array{string,int,int}>
     */
    private function groupedPictures(ZipArchive $zip): array
    {
        $drawingPath = $this->sheetDrawingPath($zip);
        $xml = $drawingPath ? $zip->getFromName($drawingPath) : false;
        if ($xml === false) {
            return [];
        }
        $rels = $this->readRels($zip, $drawingPath);

        $doc = new \DOMDocument;
        $doc->loadXML($xml);
        $x = new \DOMXPath($doc);
        $x->registerNamespace('xdr', 'http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing');
        $x->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

        $out = [];
        foreach ($x->query('/xdr:wsDr/*[xdr:grpSp]') as $anchor) {
            $blip = $x->query('.//xdr:pic//a:blip', $anchor)->item(0);
            $from = $x->query('xdr:from', $anchor)->item(0);
            $rid = $blip?->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed');
            if (! $from || ! $rid || ! isset($rels[$rid])) {
                continue;
            }
            $col = (int) $x->evaluate('string(xdr:col)', $from) + 1;
            $row = (int) $x->evaluate('string(xdr:row)', $from) + 1;
            $out[] = [$rels[$rid], $col, $row];
        }

        return $out;
    }

    /** Ro'yxat varag'iga tegishli xl/drawings/drawingN.xml yo'li */
    private function sheetDrawingPath(ZipArchive $zip): ?string
    {
        $wb = simplexml_load_string((string) $zip->getFromName('xl/workbook.xml'));
        $wbRels = $this->readRels($zip, 'xl/workbook.xml');
        foreach ($wb->sheets->sheet as $sheet) {
            if ((string) $sheet['name'] !== $this->ws->getTitle()) {
                continue;
            }
            $rid = (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
            $sheetPath = $wbRels[$rid] ?? null;
            foreach ($sheetPath ? $this->readRels($zip, $sheetPath, true) : [] as [$type, $target]) {
                if (str_ends_with($type, '/drawing')) {
                    return $target;
                }
            }
        }

        return null;
    }

    /**
     * .rels faylini o'qiydi: Id -> to'liq yo'l (yoki $withType bo'lsa [tur, yo'l] ro'yxati)
     */
    private function readRels(ZipArchive $zip, string $part, bool $withType = false): array
    {
        $relsPath = dirname($part).'/_rels/'.basename($part).'.rels';
        $xml = $zip->getFromName($relsPath);
        if ($xml === false) {
            return [];
        }
        $out = [];
        foreach (simplexml_load_string($xml)->Relationship as $r) {
            $target = (string) $r['Target'];
            $full = str_starts_with($target, '/') ? ltrim($target, '/') : $this->normalizePath(dirname($part).'/'.$target);
            $withType ? $out[] = [(string) $r['Type'], $full] : $out[(string) $r['Id']] = $full;
        }

        return $out;
    }

    private function normalizePath(string $path): string
    {
        $parts = [];
        foreach (explode('/', $path) as $p) {
            if ($p === '..') {
                array_pop($parts);
            } elseif ($p !== '.' && $p !== '') {
                $parts[] = $p;
            }
        }

        return implode('/', $parts);
    }
}
