<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Creates the first admin.
 *
 * Reads ADMIN_EMAIL and ADMIN_PASSWORD from the environment. With no password
 * set it generates one and prints it once, so nothing weak or shared ever ends
 * up committed or sitting in shell history.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'hello@jplevi.com');
        $password = env('ADMIN_PASSWORD');
        $generated = false;

        if (blank($password)) {
            $password = Str::password(20);
            $generated = true;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => env('ADMIN_NAME', 'Robert Jean Pierre'), 'password' => bcrypt($password)],
        );

        $user->syncRoles(['admin']);

        $this->command->info("Admin: {$email} [".$user->getRoleNames()->implode(', ').']');

        if ($generated) {
            $this->command->warn("Generated password: {$password}");
            $this->command->warn('Shown once. Change it after first sign in.');
        }
    }
}
