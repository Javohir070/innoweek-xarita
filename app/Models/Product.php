<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'expo_products';

    protected $fillable = ['place_id', 'position', 'name', 'images'];

    protected function casts(): array
    {
        return ['images' => 'array'];
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
