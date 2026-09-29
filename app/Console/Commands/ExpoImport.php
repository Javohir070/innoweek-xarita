<?php

namespace App\Console\Commands;

use App\Models\Place;
use App\Services\ExpoImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Excel'dan olingan ma'lumotni (update.py yaratgan data.json) va rasmlarni bazaga yuklaydi.
 *   php artisan expo:import                       # database/data/expo.json + database/data/expo-img
 *   php artisan expo:import yol/data.json --images=yol/img --force
 */
class ExpoImport extends Command
{
    protected $signature = 'expo:import {json? : data.json fayli} {--images= : rasmlar papkasi} {--force : mavjud ma\'lumotni o\'chirib qayta yuklash}';

    protected $description = "Ko'rgazma ro'yxati va rasmlarini JSON fayldan bazaga yuklash";

    public function handle(ExpoImporter $importer): int
    {
        $json = $this->argument('json') ?: database_path('data/expo.json');
        $imgDir = $this->option('images') ?: database_path('data/expo-img');
        if (! is_file($json)) {
            $this->error("Fayl topilmadi: $json");

            return self::FAILURE;
        }

        if (Place::exists() && ! $this->option('force')) {
            $this->warn("Bazada allaqachon ma'lumot bor. Sayt orqali kiritilgan o'zgarishlar o'chib ketadi.");
            $this->line('Baribir qayta yuklash uchun: php artisan expo:import --force');

            return self::FAILURE;
        }

        $rows = json_decode(File::get($json), true, flags: JSON_THROW_ON_ERROR);
        $base = config('expo.image_dir');
        $disk = Storage::disk('public');

        // rasmlarni public diskka nusxalash
        $copied = 0;
        if (is_dir($imgDir)) {
            foreach (File::files($imgDir) as $file) {
                $disk->put("$base/".$file->getFilename(), File::get($file->getPathname()));
                $copied++;
            }
        }

        // "p001.jpg" -> "expo/p001.jpg", "uploads/x.jpg" -> "expo/uploads/x.jpg"
        foreach ($rows as &$row) {
            foreach ($row['products'] ?? [] as $i => $p) {
                $row['products'][$i]['img'] = array_map(fn ($f) => "$base/".ltrim($f, '/'), $p['img'] ?? []);
            }
        }
        unset($row);
        $count = $importer->replaceAll($rows);

        $this->info("$count ta joy va $copied ta rasm yuklandi.");

        return self::SUCCESS;
    }
}
