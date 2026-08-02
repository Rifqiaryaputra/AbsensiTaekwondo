<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedAdmin();
    }

    /**
     * Akun Admin awal agar dapat login & menguji sistem.
     */
    private function seedAdmin(): void
    {
        User::factory()->create([
            'name' => 'Admin UKM Taekwondo',
            'email' => 'admin@taekwondo.com',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'force_password_change' => false,
            'email_verified_at' => now(),
        ]);
    }
}
