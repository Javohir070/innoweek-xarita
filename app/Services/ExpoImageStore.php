<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Rasmlarni kichraytirib, JPEG qilib "public" diskka saqlaydi */
class ExpoImageStore
{
    /** Admin yuklagan rasm -> expo/uploads/... */
    public function store(UploadedFile $file): string
    {
        $dir = config('expo.image_dir').'/uploads';
        $name = now()->format('Ymd').'_'.Str::lower(Str::random(12));

        $jpeg = $this->toJpeg((string) file_get_contents($file->getRealPath()), config('expo.upload_max_side'));
        if ($jpeg === null) {
            // GD bo'lmasa yoki rasmni o'qib bo'lmasa — asl fayl (turi validatsiyada tekshirilgan)
            return $file->storeAs($dir, $name.'.'.$file->extension(), 'public');
        }

        Storage::disk('public')->put("$dir/$name.jpg", $jpeg);

        return "$dir/$name.jpg";
    }

    /** Excel ichidagi rasm -> expo/{name}.jpg (o'qib bo'lmasa asl ko'rinishda) */
    public function putBinary(string $binary, string $name, string $ext): string
    {
        $jpeg = $this->toJpeg($binary, 1000);
        $path = config('expo.image_dir').'/'.$name.'.'.($jpeg === null ? $ext : 'jpg');
        Storage::disk('public')->put($path, $jpeg ?? $binary);

        return $path;
    }

    private function toJpeg(string $binary, int $maxSide): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }
        $src = @imagecreatefromstring($binary);
        if ($src === false) {
            return null;
        }

        $w = imagesx($src);
        $h = imagesy($src);
        $k = min(1, $maxSide / max($w, $h));
        $nw = max(1, (int) round($w * $k));
        $nh = max(1, (int) round($h * $k));

        $dst = imagecreatetruecolor($nw, $nh);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // shaffof fon -> oq
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        ob_start();
        imagejpeg($dst, null, 82);
        $out = ob_get_clean();
        imagedestroy($src);
        imagedestroy($dst);

        return $out ?: null;
    }
}
