<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $table = 'expo_places';

    protected $fillable = ['stand', 'place', 'cont', 'section', 'org', 'info', 'contact', 'dept'];

    protected function casts(): array
    {
        return ['place' => 'integer', 'cont' => 'integer'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('position');
    }

    /** Stend yoki zonadagi joylar soni; bunday stend/zona bo'lmasa 0 */
    public static function maxPlace(string $stand): int
    {
        if (in_array($stand, config('expo.stands'), true)) {
            return config('expo.places_per_stand');
        }

        return config('expo.zones')[$stand]['places'] ?? 0;
    }

    /** Barcha joylar xarita tartibida (avval stendlar, keyin zonalar; har birida 1..N) — sahifa kutadigan ko'rinishda */
    public static function forMap(): array
    {
        $order = array_flip([...config('expo.stands'), ...array_keys(config('expo.zones'))]);

        return static::with('products')->get()
            ->sortBy(fn (Place $p) => [($order[$p->stand] ?? 999), $p->place])
            ->values()
            ->map(fn (Place $p) => $p->toMapArray())
            ->all();
    }

    public function toMapArray(): array
    {
        return [
            'stand' => $this->stand,
            'place' => (string) $this->place,
            'cont' => $this->cont ? (string) $this->cont : null,
            'section' => $this->section,
            'org' => $this->org,
            'info' => (string) $this->info,
            'contact' => (string) $this->contact,
            'dept' => (string) $this->dept,
            'products' => $this->products->map(fn (Product $x) => [
                'name' => $x->name,
                'img' => array_values($x->images ?? []),
            ])->all(),
        ];
    }
}
