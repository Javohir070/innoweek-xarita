<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('expo_places', function (Blueprint $table) {
            $table->id();
            $table->string('stand', 8);
            $table->unsignedTinyInteger('place');
            // bir tashkilot bir nechta joyni egallasa: shu stenddagi asosiy joy raqami
            $table->unsignedTinyInteger('cont')->nullable();
            $table->string('section', 300)->default('');
            $table->string('org', 1000)->default('');
            $table->text('info')->nullable();
            $table->text('contact')->nullable();
            $table->text('dept')->nullable();
            $table->timestamps();

            $table->unique(['stand', 'place']);
        });

        Schema::create('expo_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('expo_places')->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('name', 500)->default('');
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expo_products');
        Schema::dropIfExists('expo_places');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
