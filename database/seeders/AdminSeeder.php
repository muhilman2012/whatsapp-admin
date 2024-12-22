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
                'username' => 'Vimala Asty',
                'nama' => 'Vimala Asty',
                'email' => 'vimala.asty@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Muda',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => 'Asisten Deputi Tata Kelola Pemerintahan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
            'username' => 'Benny Iswardi',
            'nama' => 'Benny Iswardi',
            'email' => 'benny.iswardi@set.wapresri.go.id',
            'password' => Hash::make('SETwapres@2024#'),
            'country' => 'Indonesia',
            'phone' => '081234567890',
            'born' => '2024-11-11',
            'avatar' => 'sample-images.png',
            'address' => 'Jl. Kebon Sirih 14, Jakarta',
            'role' => 'analis',
            'jabatan' => 'Analis Kebijakan Madya',
            'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
            'unit' => 'Asisten Deputi Tata Kelola Pemerintahan',
            'created_at' => now(),
            'updated_at' => now(),
    ]);
    }
}
