<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\admins;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tambah akun analis
        admins::create([
            'username' => 'Analis 1',
                'email' => 'analis1@gmail.com',
                'password' => Hash::make('analis123'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis', // Role default
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }
}
