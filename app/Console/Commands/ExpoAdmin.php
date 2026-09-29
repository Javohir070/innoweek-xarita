<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * Admin yaratish yoki parolini almashtirish:
 *   php artisan expo:admin admin@example.uz --name="Admin"
 */
class ExpoAdmin extends Command
{
    protected $signature = 'expo:admin {email} {--name=Admin}';

    protected $description = "Ko'rgazma admini yaratish yoki parolini yangilash";

    public function handle(): int
    {
        $email = strtolower($this->argument('email'));
        $password = $this->secret('Parol (kamida 8 belgi)');
        $confirm = $this->secret('Parolni takrorlang');

        $v = Validator::make(
            ['email' => $email, 'password' => $password, 'password_confirmation' => $confirm],
            ['email' => 'required|email', 'password' => 'required|string|min:8|confirmed'],
        );
        if ($v->fails()) {
            foreach ($v->errors()->all() as $msg) {
                $this->error($msg);
            }

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $this->option('name'), 'password' => $password],
        );
        $user->forceFill(['is_admin' => true])->save();

        $this->info(($user->wasRecentlyCreated ? 'Admin yaratildi' : 'Admin yangilandi').": $email");

        return self::SUCCESS;
    }
}
