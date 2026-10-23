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

    /** Excel ichidagi rasm -> expo/{name}.jpg (o'qib bo'lmasa asl ko'rinishda; brauzer ko'rsata olmaydigan tur bo'lsa null) */
    public function putBinary(string $binary, string $name, string $ext): ?string
    {
        if ($ext === 'emf') {
            // ichida oddiy rasm bo'lsa, o'sha olinadi (aks holda quyida null qaytadi)
            $binary = $this->emfBitmap($binary) ?? $binary;
        }
        $jpeg = $this->toJpeg($binary, 1000);
        if ($jpeg === null && ! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return null;
        }
        $path = config('expo.image_dir').'/'.$name.'.'.($jpeg === null ? $ext : 'jpg');
        Storage::disk('public')->put($path, $jpeg ?? $binary);

        return $path;
    }

    /**
     * EMF (Windows metafayli) ichidagi rasm: EMR_STRETCHDIBITS yozuvidagi bitmap BMP fayl qilib qaytariladi.
     * Excel'ga nusxalab qo'yilgan rasm ba'zan shunday saqlanadi. Faqat vektor chizmali EMF uchun null.
     */
    private function emfBitmap(string $emf): ?string
    {
        $len = strlen($emf);
        for ($pos = 0; $pos + 8 <= $len;) {
            ['type' => $type, 'size' => $size] = unpack('Vtype/Vsize', $emf, $pos);
            if ($size < 8 || $pos + $size > $len) {
                return null;
            }
            if ($type === 81 && $size >= 80) {   // EMR_STRETCHDIBITS: 48-baytdan bitmap sarlavhasi va piksellar joyi
                $r = unpack('VbmiOff/VbmiSize/VbitsOff/VbitsSize', $emf, $pos + 48);
                if ($r['bmiSize'] < 40 || $r['bitsSize'] < 1
                    || $r['bmiOff'] + $r['bmiSize'] > $size || $r['bitsOff'] + $r['bitsSize'] > $size) {
                    return null;
                }
                $head = 14 + $r['bmiSize'];

                return 'BM'.pack('VvvV', $head + $r['bitsSize'], 0, 0, $head)
                    .substr($emf, $pos + $r['bmiOff'], $r['bmiSize']).substr($emf, $pos + $r['bitsOff'], $r['bitsSize']);
            }
            $pos += $size;
        }

        return null;
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
