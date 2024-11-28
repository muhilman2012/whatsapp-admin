<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            [
                'username' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'admin', // Role default
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Deputi Ekonomi',
                'email' => 'deputi1@gmail.com',
                'password' => Hash::make('deputi123'),
                'country' => 'Indonesia',
                'phone' => '081111111111',
                'born' => '1980-01-01',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_1', // Deputi 1
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Deputi Pembangunan Manusia',
                'email' => 'deputi2@gmail.com',
                'password' => Hash::make('deputi123'),
                'country' => 'Indonesia',
                'phone' => '082222222222',
                'born' => '1982-02-02',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_2', // Deputi 2
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Deputi Administrasi',
                'email' => 'deputi3@gmail.com',
                'password' => Hash::make('deputi123'),
                'country' => 'Indonesia',
                'phone' => '083333333333',
                'born' => '1983-03-03',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_3', // Deputi 3
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Deputi Pemerintahan',
                'email' => 'deputi4@gmail.com',
                'password' => Hash::make('deputi123'),
                'country' => 'Indonesia',
                'phone' => '084444444444',
                'born' => '1984-04-04',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_4', // Deputi 4
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
