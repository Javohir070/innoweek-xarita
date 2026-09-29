# Ko'rgazma xaritasi 2026 (Laravel)

Interaktiv stend xaritasi va ishtirokchilar ro'yxati. Adminlar saytning o'zida ishtirokchi qo'shadi,
tahrirlaydi, o'chiradi va mahsulot rasmlarini yuklaydi.

- Laravel 12, PHP 8.2+, MySQL/MariaDB
- Node/npm **kerak emas** — sahifa Blade shablon, CSS/JS ichida
- PHP kengaytmalari: `pdo_mysql`, `gd` (rasmlarni siqish uchun), `fileinfo`, `mbstring`

## Serverga o'rnatish

```bash
git clone … vystavka && cd vystavka          # yoki papkani serverga ko'chiring
composer install --no-dev --optimize-autoloader
cp .env.example .env                        # DB_* va APP_URL ni to'ldiring
php artisan key:generate
php artisan migrate --seed                  # jadvallar + 240 joy va 326 rasm (yoki: expo:import-excel fayl.xlsx)
php artisan storage:link
php artisan expo:admin admin@misol.uz --name="Admin"   # parolni so'raydi
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Veb-server `public/` papkaga qaratiladi. `storage/` va `bootstrap/cache/` ga veb-server foydalanuvchisi
yoza olishi kerak (`chown -R www-data storage bootstrap/cache`). HTTPS ni albatta yoqing.

Nginx misoli:

```nginx
server {
    server_name expo.misol.uz;
    root /var/www/vystavka/public;
    index index.php;
    client_max_body_size 20M;              # rasm yuklash uchun

    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }
}
```

PHP'da ham `upload_max_filesize = 16M` va `post_max_size = 20M` bo'lsin.

## Foydalanish

- `/` — xarita (hamma ko'radi). Katakchani bosing — joy haqida ma'lumot.
- **Admin kirish** tugmasi → `/login`. Kirgandan keyin modalda **Tahrirlash / O'chirish**,
  bo'sh joyda **Ma'lumot qo'shish**, "Ro'yxat" bo'limida **Yangi ishtirokchi** tugmalari chiqadi.
- Ro'yxatdan o'tish yo'q. Admin qo'shish yoki parolini almashtirish: `php artisan expo:admin email`.

## Ma'lumotlar

| Qayerda | Nima |
|---|---|
| `expo_places`, `expo_products` jadvallari | Joylar, tashkilotlar, mahsulotlar |
| `storage/app/public/expo/` | Excel'dan olingan rasmlar |
| `storage/app/public/expo/uploads/` | Admin yuklagan rasmlar |
| `database/data/expo.json`, `database/data/expo-img/` | Boshlang'ich ma'lumot (seed) |

Zaxira nusxa: baza (`mysqldump`) + `storage/app/public/expo/` papkasi.

## Excel'dan yuklash

Excel faylini ("Рўйхат" varag'i — ro'yxat, birlashtirilgan kataklar va mahsulot rasmlari) to'g'ridan-to'g'ri bazaga:

```bash
php artisan expo:import-excel "Выставка_заключительная_рассадка_29_09.xlsx"
php artisan expo:import-excel yangi.xlsx --force     # bazada ma'lumot bo'lsa
```

**Diqqat:** `--force` bazadagi barcha joylarni almashtiradi — sayt orqali kiritilgan o'zgarishlar yo'qoladi.
Eski usul (Python `update.py` yaratgan `data.json`): `php artisan expo:import data.json --images=img --force`.

## Stendlar

Stendlar ro'yxati `config/expo.php` da, xaritadagi joylashuvi va ranglari
`resources/views/expo.blade.php` dagi `STANDS` / `LABELS` / `DIRS` massivlarida.

## Testlar

```bash
php artisan test
```
