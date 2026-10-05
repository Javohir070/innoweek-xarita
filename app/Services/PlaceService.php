<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Support\Facades\DB;

class PlaceService
{
    /** Joyni yaratadi yoki to'liq almashtiradi (mahsulotlar ham qayta yoziladi) */
    public function save(string $stand, int $number, array $data): Place
    {
        return DB::transaction(function () use ($stand, $number, $data) {
            $place = Place::firstOrNew(['stand' => $stand, 'place' => $number]);
            $place->fill([
                'cont' => null,
                'section' => (string) ($data['section'] ?? ''),
                'org' => trim($data['org']),
                'info' => (string) ($data['info'] ?? ''),
                'contact' => (string) ($data['contact'] ?? ''),
                'dept' => (string) ($data['dept'] ?? ''),
            ])->save();
            // "davomi" joylar tashkilot nomining nusxasini saqlaydi — ro'yxatda eskirib qolmasin
            Place::where('stand', $stand)->where('cont', $number)->update(['org' => $place->org]);

            $place->products()->delete();
            foreach (array_values($data['products'] ?? []) as $i => $product) {
                $name = trim((string) ($product['name'] ?? ''));
                $images = array_values($product['img'] ?? []);
                if ($name === '' && ! $images) {
                    continue;
                }
                $place->products()->create(['position' => $i, 'name' => $name, 'images' => $images]);
            }

            return $place->load('products');
        });
    }

    /** Joyni bo'shatadi; unga "davomi" sifatida bog'langan joylar ham bo'shaydi. Yo'nalish (section) qoladi. */
    public function clear(string $stand, int $number): void
    {
        DB::transaction(function () use ($stand, $number) {
            $places = Place::where('stand', $stand)
                ->where(fn ($q) => $q->where('place', $number)->orWhere('cont', $number))
                ->get();

            foreach ($places as $place) {
                $place->products()->delete();
                $place->update(['org' => '', 'info' => '', 'contact' => '', 'dept' => '', 'cont' => null]);
            }
        });
    }
}
