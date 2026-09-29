<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Boshlang'ich ro'yxat (Excel'dan) — baza bo'sh bo'lsagina yuklanadi.
     * Admin foydalanuvchi alohida yaratiladi: php artisan expo:admin email@misol.uz
     */
    public function run(): void
    {
        if (! Place::exists()) {
            Artisan::call('expo:import', [], $this->command->getOutput());
        }
    }
}
