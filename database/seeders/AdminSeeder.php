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
                'username' => 'Purwono Prihantoro Budi Trisnanto',
                'nama' => 'Purwono Prihantoro Budi Trisnanto',
                'email' => 'purwono.prihantoro@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_4',
                'jabatan' => 'Kepala Biro Perencanaan dan Keuangan',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Perencanaan dan Keuangan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Yayat Hidayat',
                'nama' => 'Yayat Hidayat',
                'email' => 'yayat.hidayat@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_4',
                'jabatan' => 'Kepala Biro Tata Usaha,Teknologi Informasi, dan Kepegawaian',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Tata Usaha,Teknologi Informasi, dan Kepegawaian',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Rusmin Nuryadin',
                'nama' => 'Rusmin Nuryadin',
                'email' => 'rusmin.nuryadin@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_4',
                'jabatan' => 'Kepala Biro Pers, Media, dan Informasi,',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Pers, Media, dan Informasi,',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Erick Griwantara',
                'nama' => 'Erick Griwantara',
                'email' => 'erick.griwantara@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_4',
                'jabatan' => 'Kepala Biro Protokol dan Kerumahtanggaan',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Protokol dan Kerumahtanggaan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Kepala Biro Umum',
                'nama' => 'Kepala Biro Umum',
                'email' => 'biro.umum@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'deputi_4',
                'jabatan' => 'Kepala Biro Umum',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Umum',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Aldi Yarman',
                'nama' => 'Aldi Yarman',
                'email' => 'aldi.yarman@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Madya',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
                'unit' => 'Asisten Deputi Ekonomi dan Keuangan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Tiapul Elfrida Simanungkalit',
                'nama' => 'Tiapul Elfrida Simanungkalit',
                'email' => 'tiapul.elfrida@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Madya',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
                'unit' => 'Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }
}
