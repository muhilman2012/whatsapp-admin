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
                'username' => 'Slamet Widodo',
                'nama' => 'Slamet Widodo',
                'email' => 'slamet.widodo@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_2',
                'jabatan' => 'Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia',
                'unit' => 'Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Tuti Trihastuti Sukardi',
                'nama' => 'Tuti Trihastuti Sukardi',
                'email' => 'tuti.trihastuti@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_2',
                'jabatan' => 'Asisten Deputi Pembangunan Sumber Daya Manusia',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia',
                'unit' => 'Asisten Deputi Pembangunan Sumber Daya Manusia',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Adyawarman',
                'nama' => 'Adyawarman',
                'email' => 'adyawarman@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_2',
                'jabatan' => 'Asisten Deputi Penanggulangan Kemiskinan',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia',
                'unit' => 'Asisten Deputi Penanggulangan Kemiskinan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Pranggono Dwianto',
                'nama' => 'Pranggono Dwianto',
                'email' => 'pranggono.dwianto@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_3',
                'jabatan' => 'Asisten Deputi Tata Kelola Pemerintahan',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => 'Asisten Deputi Tata Kelola Pemerintahan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Muharromi',
                'nama' => 'Muharromi',
                'email' => 'muharromi@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_3',
                'jabatan' => 'Asisten Deputi Wawasan Kebangsaan, Pertahanan, dan Keamanan',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => 'Asisten Deputi Wawasan Kebangsaan, Pertahanan, dan Keamanan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Afif Juniar',
                'nama' => 'Afif Juniar',
                'email' => 'afif.juniar@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_3',
                'jabatan' => 'Asisten Deputi Politik, Hukum, dan Otonomi Daerah',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => 'Asisten Deputi Politik, Hukum, dan Otonomi Daerah',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Lukman Hakim Siregar',
                'nama' => 'Lukman Hakim Siregar',
                'email' => 'lukman.siregar@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_3',
                'jabatan' => 'Asisten Deputi Hubungan Luar Negeri',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => 'Asisten Deputi Hubungan Luar Negeri',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }
}
