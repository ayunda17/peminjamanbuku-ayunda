<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
            // 'name' => 'Test User',
            // 'email' => 'test@example.com',
            // 'role' => 'user',
        // ]);

        User::create([
            'name' => 'Admin',
            'nis' => '00000000',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'is_active' => 1,
            'pending_status' => 'approved',
            'role' => 'admin',
        ]);

        $this->call([
            PenanggungJawabSeeder::class,
        ]);
    }
}
