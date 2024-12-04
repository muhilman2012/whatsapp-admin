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
                'username' => 'Sespri Wapres',
                'email' => 'sespri@set.wapresri.go.id',
                'password' => Hash::make('$espr1S3twapre$'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'admin', // Role default
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
