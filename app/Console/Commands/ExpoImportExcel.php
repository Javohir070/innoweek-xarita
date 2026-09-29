<?php

namespace App\Console\Commands;

use App\Models\Place;
use App\Services\ExcelExpoReader;
use App\Services\ExpoImporter;
use Illuminate\Console\Command;

/**
 * Excel faylidan (ro'yxat + rasmlar) bazaga yuklash:
 *   php artisan expo:import-excel "Выставка_заключительная_рассадка_29_09.xlsx"
 */
class ExpoImportExcel extends Command
{
    protected $signature = 'expo:import-excel {file : .xlsx fayl} {--force : bazadagi mavjud ma\'lumotni almashtirish}';

    protected $description = "Ko'rgazma ro'yxati va rasmlarini Excel'dan bazaga yuklash";

    public function handle(ExcelExpoReader $reader, ExpoImporter $importer): int
    {
        $file = $this->argument('file');
        if (! is_file($file)) {
            $this->error("Fayl topilmadi: $file");

            return self::FAILURE;
        }
        if (Place::exists() && ! $this->option('force')) {
            $this->warn("Bazada allaqachon ma'lumot bor. Sayt orqali kiritilgan o'zgarishlar o'chib ketadi.");
            $this->line('Baribir almashtirish uchun --force qo\'shing.');

            return self::FAILURE;
        }

        ini_set('memory_limit', '1024M');
        $this->info("Excel o'qilmoqda...");
        $bar = $this->output->createProgressBar();
        $rows = $reader->read($file, fn () => $bar->advance());
        $bar->finish();
        $this->newLine();

        $count = $importer->replaceAll($rows);
        $images = collect($rows)->flatMap(fn ($r) => collect($r['products'])->flatMap(fn ($p) => $p['img']))->unique()->count();
        $filled = collect($rows)->where('org', '!=', '')->count();

        $this->info("$count ta joy yuklandi ($filled tasi band), $images ta rasm.");

        return self::SUCCESS;
    }
}
