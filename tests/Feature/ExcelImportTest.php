<?php

namespace Tests\Feature;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    private function addImages(Worksheet $ws, array $cells, int $offsetY = 0): void
    {
        $img = tempnam(sys_get_temp_dir(), 'img').'.png';
        imagepng(imagecreatetruecolor(40, 30), $img);
        foreach ($cells as $cell) {
            (new Drawing)->setPath($img)->setCoordinates($cell)->setDescription('Internetdan olingan izoh')->setOffsetY($offsetY)->setWorksheet($ws);
        }
    }

    private function save(Spreadsheet $book): string
    {
        $file = tempnam(sys_get_temp_dir(), 'expo').'.xlsx';
        IOFactory::createWriter($book, 'Xlsx')->save($file);

        return $file;
    }

    /** Yangi ko'rinish: "Жой" oraliq bilan, mas'ul boshqarma ustuni yo'q, mahsulotlar H dan */
    private function makeExcel(): string
    {
        $book = new Spreadsheet;
        $book->getActiveSheet()->setTitle('2026');
        $ws = $book->createSheet()->setTitle('Рўйхат');
        $ws->fromArray(['Т/р', 'Yo\'nalish', 'Павильон', 'Жой', 'Ташкилот номи', 'Маълумот', 'Масъул', 'Ишланма расми'], null, 'A3');
        // A1-1..2: bitta tashkilot ikki joyda, kirillcha "А1"; rasm nom katagining o'ngida (I, K)
        $ws->fromArray([1, 'Hudud', 'А1', '1-2', 'Andijon', "1. Chiroq\n2. Sirop", 'Ali (90) 111-22-33', '1. Chiroq', null, '2. Sirop'], null, 'A4');
        // A1-3: bo'sh joy
        $ws->fromArray([2, 'Hudud', 'А1', 3], null, 'A5');
        // A1-4: tashkilot yozilmagan, faqat loyiha nomi; rasm H katagida, nomsiz
        $ws->fromArray([3, 'Hudud', 'А1', 4, null, 'Educoin', '-'], null, 'A6');
        // B4-1..10: nomsiz bir nechta rasm — bitta ishlanma
        $ws->fromArray([4, '7TECH', 'B4', '1-10', '7TECH', 'Aqlli uy', 'Bobur'], null, 'A7');
        // B6-1..4: "Жой" katagi birlashtirilgan — uch tashkilot oraliqni bo'lishadi (2 + 1 + 1)
        $ws->fromArray([5, 'Sanoat', 'B6', '1-4', 'Vazirlik', 'V'], null, 'A8');
        $ws->fromArray([6, 'Sanoat', 'B6', null, 'Kombinat', 'K'], null, 'A9');
        $ws->fromArray([7, 'Sanoat', 'B6', null, 'Zavod', 'Z'], null, 'A10');
        $ws->mergeCells('D8:D10');
        // xato qatorlar: band joy, mavjud bo'lmagan joy, tushunarsiz joy
        $ws->fromArray([8, 'Hudud', 'А1', 2, 'Takror'], null, 'A11');
        $ws->fromArray([9, 'Hudud', 'А1', 21, 'Ortiqcha'], null, 'A12');
        $ws->fromArray([10, 'Hudud', 'А1', 'kirish', 'Tushunarsiz'], null, 'A13');

        $this->addImages($ws, ['I4', 'K4', 'H6', 'H7', 'I7', 'J7']);
        // 5-qator katagining eng pastidan boshlangan rasm aslida 6-qatorda turibdi
        $ws->getRowDimension(5)->setRowHeight(30);
        $this->addImages($ws, ['I5'], 38);

        return $this->save($book);
    }

    /** Eski ko'rinish: har bir joy alohida qatorda, H — mas'ul boshqarma, mahsulotlar I dan */
    private function makeOldExcel(): string
    {
        $book = new Spreadsheet;
        $ws = $book->getActiveSheet()->setTitle('Рўйхат');
        $ws->fromArray(['Т/р', 'Yo\'nalish', 'Павильон', 'Жой', 'Ташкилот', 'Маълумот', 'Масъул', 'Бошқарма'], null, 'A3');
        $ws->fromArray([1, 'Hudud', 'А1', 1, 'Andijon', "1. Chiroq\n2. Sirop", 'Ali', 'Boshqarma'], null, 'A4');
        $ws->fromArray([2, 'Hudud', 'А1', 2], null, 'A5');
        $ws->mergeCells('E4:E5');
        $ws->setCellValue('I4', '1. Chiroq');
        $ws->setCellValue('K4', '2. Sirop');
        $this->addImages($ws, ['I4']);

        return $this->save($book);
    }

    public function test_excel_is_imported_with_ranges_and_images(): void
    {
        Storage::fake('public');

        $this->artisan('expo:import-excel', ['file' => $this->makeExcel()])
            ->expectsOutputToContain('kirish')
            ->expectsOutputToContain('(A1-2)')
            ->expectsOutputToContain('(A1-21)')
            ->assertSuccessful();

        $this->assertSame(18, Place::count());

        $a1 = Place::where(['stand' => 'A1', 'place' => 1])->with('products')->first();
        $this->assertSame('Andijon', $a1->org);
        $this->assertNull($a1->cont);
        $this->assertSame('', (string) $a1->dept);
        $this->assertSame(['1. Chiroq', '2. Sirop'], $a1->products->pluck('name')->all());
        $this->assertCount(1, $a1->products[0]->images);
        $this->assertCount(1, $a1->products[1]->images);
        Storage::disk('public')->assertExists($a1->products[0]->images[0]);

        $a2 = Place::where(['stand' => 'A1', 'place' => 2])->with('products')->first();
        $this->assertSame(['Andijon', 1, ''], [$a2->org, $a2->cont, (string) $a2->info]);
        $this->assertCount(0, $a2->products);

        $a3 = Place::where(['stand' => 'A1', 'place' => 3])->with('products')->first();
        $this->assertSame('', $a3->org);
        $this->assertCount(0, $a3->products); // pastga osilib turgan rasm keyingi qatorga tegishli

        $a4 = Place::where(['stand' => 'A1', 'place' => 4])->with('products')->first();
        $this->assertSame(['Educoin', '', ''], [$a4->org, (string) $a4->info, (string) $a4->contact]);
        $this->assertSame('', $a4->products[0]->name); // rasm izohi nom sifatida olinmaydi
        $this->assertCount(2, $a4->products[0]->images); // o'z rasmi + yuqori qatordan osilib tushgani

        $b4 = Place::where('stand', 'B4')->with('products')->orderBy('place')->get();
        $this->assertCount(10, $b4);
        $this->assertCount(1, $b4[0]->products);
        $this->assertSame([null, 1, 1], [$b4[0]->cont, $b4[1]->cont, $b4[9]->cont]);

        $b6 = Place::where('stand', 'B6')->orderBy('place')->get();
        $this->assertSame(['Vazirlik', 'Vazirlik', 'Kombinat', 'Zavod'], $b6->pluck('org')->all());
        $this->assertSame([null, 1, null, null], $b6->pluck('cont')->all());
    }

    public function test_old_layout_with_dept_column_is_still_imported(): void
    {
        Storage::fake('public');

        $this->artisan('expo:import-excel', ['file' => $this->makeOldExcel()])->assertSuccessful();

        $a1 = Place::where(['stand' => 'A1', 'place' => 1])->with('products')->first();
        $this->assertSame(['Andijon', 'Boshqarma'], [$a1->org, $a1->dept]);
        $this->assertSame(['1. Chiroq', '2. Sirop'], $a1->products->pluck('name')->all());
        $this->assertCount(1, $a1->products[0]->images);
        $a2 = Place::where(['stand' => 'A1', 'place' => 2])->first();
        $this->assertSame(['Andijon', 1], [$a2->org, $a2->cont]);
    }

    public function test_excel_import_requires_force_when_data_exists(): void
    {
        Storage::fake('public');
        Place::create(['stand' => 'A1', 'place' => 1, 'org' => 'Mavjud']);

        $this->artisan('expo:import-excel', ['file' => $this->makeExcel()])->assertFailed();
        $this->assertSame('Mavjud', Place::first()->org);

        $this->artisan('expo:import-excel', ['file' => $this->makeExcel(), '--force' => true])->assertSuccessful();
        $this->assertSame('Andijon', Place::where(['stand' => 'A1', 'place' => 1])->value('org'));
    }
}
