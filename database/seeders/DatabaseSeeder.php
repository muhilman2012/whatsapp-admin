<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\admins;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        admins::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'phone' => '081234567890',
            'born' => '1999-01-01',
            'country' => 'Indonesia',
            'avatar' => 'sample-images.png',
            'address' => 'Jl. Jend. Sudirman No.123',
        ]);
    }
}
