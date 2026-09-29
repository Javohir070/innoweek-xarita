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
 * Ustunlar: A т/р, B yo'nalish, C павильон (stend), D жой, E tashkilot, F ishlanma haqida,
 * G mas'ul shaxs, H mas'ul boshqarma; I, K, M, O, Q, S — 1..6-mahsulot nomi va rasmi.
 * Bir tashkilot bir nechta joyni egallasa, kataklar birlashtirilgan bo'ladi — bu "cont" (davomi) sifatida saqlanadi.
 */
class ExcelExpoReader
{
    private const FIRST_ROW = 4;

    private const PRODUCT_COLS = [9, 11, 13, 15, 17, 19]; // I K M O Q S

    /** @var array<string, array{int,int}> "r:c" -> birlashtirilgan hududning yuqori-chap katagi */
    private array $top = [];

    private Worksheet $ws;

    public function __construct(private ExpoImageStore $images) {}

    /** @return array<int, array> ExpoImporter::replaceAll uchun qatorlar */
    public function read(string $file, ?callable $progress = null): array
    {
        $book = IOFactory::createReader('Xlsx')->load($file);
        $this->ws = $this->findListSheet($book->getAllSheets());
        $this->indexMerges();
        $images = $this->extractImages($file, $progress);

        $rows = [];
        $last = $this->ws->getHighestDataRow();
        for ($r = self::FIRST_ROW; $r <= $last; $r++) {
            $stand = $this->normStand($this->val($r, 3));
            if ($stand === '') {
                continue;
            }

            $products = [];
            foreach (self::PRODUCT_COLS as $slot => $c) {
                // birlashtirilgan katakning davomidagi nom takrorlanmasin
                $name = $this->isTopLeft($r, $c) ? $this->val($r, $c) : '';
                $files = $images["$r:$slot"] ?? [];
                if ($name === '' && $files) {
                    $name = $files[0]['descr'];
                }
                if ($name !== '' || $files) {
                    $products[] = ['name' => $name, 'img' => array_values(array_unique(array_column($files, 'path')))];
                }
            }

            [$org, $info, $contact, $dept] = [$this->val($r, 5), $this->val($r, 6), $this->val($r, 7), $this->val($r, 8)];
            $cont = null;
            $topRow = $this->topOf($r, 5)[0];
            if ($org !== '' && $topRow < $r) {
                $cont = $this->val($topRow, 4); // tashkilot nomi katagi yuqoridan birlashtirilgan
            }
            if ($org === '' && $this->continuesFromAbove($r)) {
                // mahsulot kataklari yuqoridan davom etadi — shu stenddagi oldingi tashkilot
                foreach (array_reverse($rows) as $prev) {
                    if ($prev['stand'] === $stand && $prev['org'] !== '') {
                        [$org, $info, $contact, $dept, $cont] = [$prev['org'], $prev['info'], $prev['contact'], $prev['dept'], $prev['place']];
                        break;
                    }
                }
            }

            $rows[] = [
                'stand' => $stand,
                'place' => (int) $this->val($r, 4),
                'cont' => $cont !== null && $cont !== '' ? (int) $cont : null,
                'section' => $this->val($r, 2),
                'org' => $org,
                'info' => $info,
                'contact' => $contact,
                'dept' => $dept,
                'products' => $products,
            ];
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
        for ($c = 9; $c <= 20; $c++) {
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

    private function normStand(string $s): string
    {
        // kirillcha А/В -> lotincha A/B
        return strtr(mb_strtoupper(trim($s)), ['А' => 'A', 'В' => 'B']);
    }

    /**
     * Rasmlarni diskka saqlaydi.
     *
     * @return array<string, array<int, array{path:string, descr:string}>> "qator:mahsulot_slot" -> rasmlar
     */
    private function extractImages(string $file, ?callable $progress): array
    {
        $zip = new ZipArchive;
        if ($zip->open($file) !== true) {
            throw new RuntimeException('Excel faylini ochib bo\'lmadi.');
        }

        // [media fayl, ustun, qator, izoh]
        $pictures = [];
        foreach ($this->ws->getDrawingCollection() as $d) {
            if ($d instanceof Drawing) {
                $media = str_contains($d->getPath(), '#') ? substr($d->getPath(), strpos($d->getPath(), '#') + 1) : $d->getPath();
                [$col, $row] = Coordinate::indexesFromString($d->getCoordinates());
                $pictures[] = [ltrim($media, '/'), $col, $row, trim((string) $d->getDescription())];
            }
        }
        array_push($pictures, ...$this->groupedPictures($zip));

        $saved = [];   // media fayl -> saqlangan yo'l (bir rasm bir necha joyda bo'lsa bir marta saqlanadi)
        $result = [];
        foreach ($pictures as [$media, $col, $row, $descr]) {
            if (! isset($saved[$media])) {
                $bin = $zip->getFromName($media);
                if ($bin === false) {
                    continue;
                }
                $saved[$media] = $this->images->putBinary($bin, 'x'.pathinfo($media, PATHINFO_FILENAME), strtolower(pathinfo($media, PATHINFO_EXTENSION)));
                $progress && $progress();
            }

            $row = $this->topOf($row, $col)[0];
            $slot = max(0, intdiv($col - 9, 2)); // I,J -> 0; K,L -> 1; ...
            $result["$row:$slot"][] = ['path' => $saved[$media], 'descr' => $descr];
        }
        $zip->close();

        return $result;
    }

    /**
     * PhpSpreadsheet guruhlangan rasmlarni (Excel'da "Группировать") o'qimaydi — ularni varaqning drawing XML'idan olamiz.
     * Guruhdan birinchi rasm olinadi.
     *
     * @return array<int, array{string,int,int,string}>
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
            $descr = (string) $x->evaluate('string(.//xdr:pic//xdr:cNvPr/@descr)', $anchor);
            $out[] = [$rels[$rid], $col, $row, trim($descr)];
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
