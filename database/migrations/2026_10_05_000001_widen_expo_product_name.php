<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Excel'da mahsulot nomi katagiga to'liq tavsif yozilgan qatorlar bor (1000 belgigacha)
    public function up(): void
    {
        Schema::table('expo_products', function (Blueprint $table) {
            $table->text('name')->change();
        });
    }

    public function down(): void
    {
        Schema::table('expo_products', function (Blueprint $table) {
            $table->string('name', 500)->default('')->change();
        });
    }
};
