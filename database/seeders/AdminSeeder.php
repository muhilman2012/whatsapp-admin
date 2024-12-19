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
                'username' => 'Robi Yunior Manuputty',
                'nama' => 'Robi Yunior Manuputty',
                'email' => 'robi.yunior@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_1',
                'jabatan' => 'Kepala Bagian Dukungan Administrasi',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
                'unit' => '',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Santi Setiawati',
                'nama' => 'Santi Setiawati',
                'email' => 'santi.setiawati@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_2',
                'jabatan' => 'Kepala Bagian Dukungan Administrasi',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia',
                'unit' => '',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Atiatul Huda',
                'nama' => 'Atiatul Huda',
                'email' => 'atiatul.huda@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_3',
                'jabatan' => 'Kepala Bagian Dukungan Administrasi',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => '',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
            admins::create([
                'username' => 'Lely Setia Rimelanty',
                'nama' => 'Lely Setia Rimelanty',
                'email' => 'lely.setia@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_4',
                'jabatan' => 'Kepala Bagian Tata Usaha, Deputi Bidang Administrasi',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => '',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }
}
