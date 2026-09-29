<?php

return [
    // Xaritadagi stendlar (tartib — ro'yxatdagi tartib). Har bir stendda 1..places_per_stand joy bor.
    'stands' => [
        'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10', 'A11', 'A12',
        'B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8', 'B9', 'B10', 'B11', 'B12',
    ],

    'places_per_stand' => 10,

    // Rasmlar "public" diskda shu papkada saqlanadi (storage/app/public/expo)
    'image_dir' => 'expo',

    // Yuklangan rasm shu o'lchamgacha kichraytiriladi (px) va JPEG sifatida saqlanadi
    'upload_max_side' => 1400,
    'upload_max_kb' => 15360,
];
