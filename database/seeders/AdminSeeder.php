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
                'username' => 'Ahmad Lutfie',
                'nama' => 'Ahmad Lutfie',
                'email' => 'ahmad.lutfie@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_1',
                'jabatan' => 'Asisten Deputi Ekonomi dan Keuangan',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
                'unit' => 'Asisten Deputi Ekonomi dan Keuangan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Celvya Betty Manurung',
                'nama' => 'Celvya Betty Manurung',
                'email' => 'betty.manurung@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_1',
                'jabatan' => 'Asisten Deputi Infrastruktur, Ketahanan Energi, dan Sumber Daya Alam',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
                'unit' => 'Asisten Deputi Infrastruktur, Ketahanan Energi, dan Sumber Daya Alam',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Abdul Mu is',
                'nama' => 'Abdul Mu is',
                'email' => 'abdul.muis@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_1',
                'jabatan' => 'Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
                'unit' => 'Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }
}
