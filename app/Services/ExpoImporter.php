<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Support\Facades\DB;

/** Barcha joylarni berilgan ro'yxat bilan almashtiradi (JSON va Excel importi uchun umumiy) */
class ExpoImporter
{
    /**
     * Har bir qator: stand, place, cont, section, org, info, contact, dept, products[{name, img[]}].
     * products[].img — "public" diskdagi yo'llar (expo/...). Qaytaradi: yuklangan joylar soni.
     */
    public function replaceAll(array $rows): int
    {
        $stands = config('expo.stands');
        $max = config('expo.places_per_stand');

        return DB::transaction(function () use ($rows, $stands, $max) {
            Place::query()->delete();
            $count = 0;
            foreach ($rows as $row) {
                $number = (int) $row['place'];
                if (! in_array($row['stand'], $stands, true) || $number < 1 || $number > $max) {
                    continue;
                }
                $place = Place::create([
                    'stand' => $row['stand'],
                    'place' => $number,
                    'cont' => ! empty($row['cont']) ? (int) $row['cont'] : null,
                    'section' => (string) ($row['section'] ?? ''),
                    'org' => (string) ($row['org'] ?? ''),
                    'info' => (string) ($row['info'] ?? ''),
                    'contact' => (string) ($row['contact'] ?? ''),
                    'dept' => (string) ($row['dept'] ?? ''),
                ]);
                foreach (array_values($row['products'] ?? []) as $i => $p) {
                    $place->products()->create([
                        'position' => $i,
                        'name' => (string) ($p['name'] ?? ''),
                        'images' => array_values($p['img'] ?? []),
                    ]);
                }
                $count++;
            }

            return $count;
        });
    }
}
