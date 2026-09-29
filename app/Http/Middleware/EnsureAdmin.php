<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Faqat is_admin = true foydalanuvchilar ma'lumotni o'zgartira oladi */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->is_admin, 403, "Bu amal uchun ruxsat yo'q");

        return $next($request);
    }
}
