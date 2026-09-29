<?php

namespace Tests\Feature;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    private function makeExcel(): string
    {
        $book = new Spreadsheet;
        $book->getActiveSheet()->setTitle('2026');
        $ws = $book->createSheet()->setTitle('Рўйхат');
        $ws->fromArray(['Т/р', 'Yo\'nalish', 'Павильон', 'Жой', 'Ташкилот', 'Маълумот', 'Масъул', 'Бошқарма'], null, 'A3');
        // A1-1..2: bitta tashkilot ikki joyda (E4:E5 birlashtirilgan), kirillcha "А1"
        $ws->fromArray([1, 'Hudud', 'А1', 1, 'Andijon', "1. Chiroq\n2. Sirop", 'Ali (90) 111-22-33', 'Boshqarma'], null, 'A4');
        $ws->fromArray([2, 'Hudud', 'А1', 2], null, 'A5');
        $ws->mergeCells('E4:E5');
        $ws->setCellValue('I4', '1. Chiroq');
        $ws->setCellValue('K4', '2. Sirop');
        // A1-3: bo'sh joy
        $ws->fromArray([3, 'Hudud', 'А1', 3], null, 'A6');
        // B7-1: rasm izohi mahsulot nomi bo'ladi
        $ws->fromArray([4, '7TECH', 'B7', 1, '7TECH', 'Aqlli uy', 'Bobur', 'Markaz'], null, 'A7');

        $img = tempnam(sys_get_temp_dir(), 'img').'.png';
        $gd = imagecreatetruecolor(40, 30);
        imagepng($gd, $img);
        foreach ([['I4', ''], ['I7', 'Aqlli uy tizimi']] as [$cell, $descr]) {
            $d = new Drawing;
            $d->setPath($img)->setCoordinates($cell)->setDescription($descr)->setWorksheet($ws);
        }

        $file = tempnam(sys_get_temp_dir(), 'expo').'.xlsx';
        IOFactory::createWriter($book, 'Xlsx')->save($file);

        return $file;
    }

    public function test_excel_is_imported_with_merges_and_images(): void
    {
        Storage::fake('public');

        $this->artisan('expo:import-excel', ['file' => $this->makeExcel()])->assertSuccessful();

        $this->assertSame(4, Place::count());

        $a1 = Place::where(['stand' => 'A1', 'place' => 1])->with('products')->first();
        $this->assertSame('Andijon', $a1->org);
        $this->assertNull($a1->cont);
        $this->assertSame(['1. Chiroq', '2. Sirop'], $a1->products->pluck('name')->all());
        $this->assertCount(1, $a1->products[0]->images);
        Storage::disk('public')->assertExists($a1->products[0]->images[0]);

        $a2 = Place::where(['stand' => 'A1', 'place' => 2])->first();
        $this->assertSame('Andijon', $a2->org);
        $this->assertSame(1, $a2->cont);

        $this->assertSame('', Place::where(['stand' => 'A1', 'place' => 3])->value('org'));

        $b7 = Place::where('stand', 'B7')->with('products')->first();
        $this->assertSame('Aqlli uy tizimi', $b7->products[0]->name);
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
