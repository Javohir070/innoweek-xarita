<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class SavePlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        $dir = preg_quote(config('expo.image_dir'), '/');

        return [
            'org' => ['required', 'string', 'max:1000'],
            'section' => ['nullable', 'string', 'max:300'],
            'info' => ['nullable', 'string', 'max:5000'],
            'contact' => ['nullable', 'string', 'max:1000'],
            'dept' => ['nullable', 'string', 'max:1000'],
            'products' => ['nullable', 'array', 'max:30'],
            'products.*.name' => ['nullable', 'string', 'max:500'],
            'products.*.img' => ['nullable', 'array', 'max:10'],
            // faqat o'zimizning rasm papkamizdagi mavjud fayllar
            'products.*.img.*' => [
                'bail', // regex o'tmasa, fayl tekshiruviga yetib bormaydi ("../" yo'llar)
                'string',
                "regex:/^{$dir}\/(uploads\/)?[A-Za-z0-9_.-]+\.(jpe?g|png|webp)$/",
                function (string $attribute, mixed $value, Closure $fail) {
                    if (! Storage::disk('public')->exists($value)) {
                        $fail('Rasm topilmadi.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'org.required' => 'Tashkilot nomini kiriting.',
            'products.*.img.*.regex' => "Rasm yo'li noto'g'ri.",
        ];
    }
}
