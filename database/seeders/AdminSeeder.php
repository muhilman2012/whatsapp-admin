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
                'username' => 'Linda Astuti',
                'nama' => 'Linda Astuti',
                'email' => 'linda.astuti@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Ahli Madya',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia',
                'unit' => 'Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Agung Darmawan',
                'nama' => 'Agung Darmawan',
                'email' => 'agung.darmawan@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Ahli Madya',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia',
                'unit' => 'Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Fina Hayati',
                'nama' => 'Fina Hayati',
                'email' => 'fina.hayati@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Ahli Madya',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia',
                'unit' => 'Asisten Deputi Penanggulangan Kemiskinan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Diena Tiara Sari',
                'nama' => 'Diena Tiara Sari',
                'email' => 'diena.tiara@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Ahli Pertama',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia',
                'unit' => 'Asisten Deputi Penanggulangan Kemiskinan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Anggraeni Puspita',
                'nama' => 'Anggraeni Puspita',
                'email' => 'anggraeni.puspita@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Muda',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => 'Asisten Deputi Politik, Hukum, dan Otonomi Daerah',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Donny Widhyanto',
                'nama' => 'Donny Widhyanto',
                'email' => 'donny.widhyanto@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Madya',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => 'Asisten Deputi Politik, Hukum, dan Otonomi Daerah',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Mohamad Soleh',
                'nama' => 'Mohamad Soleh',
                'email' => 'm.soleh@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Kebijakan Ahli Madya',
                'deputi' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
                'unit' => 'Asisten Deputi Politik, Hukum, dan Otonomi Daerah',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Rianita Kumalasari',
                'nama' => 'Rianita Kumalasari',
                'email' => 'rianita.kumalasari@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'analis',
                'jabatan' => 'Analis Anggaran Ahli Madya',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Perencanaan dan Keuangan',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Ayu Setiarini',
                'nama' => 'Ayu Setiarini',
                'email' => 'ayu.setiarini@setneg.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'admin',
                'jabatan' => 'Pranata Komputer Ahli Madya',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Tata Usaha,Teknologi Informasi, dan Kepegawaian',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Dadang Sulaeman',
                'nama' => 'Dadang Sulaeman',
                'email' => 'dadang.sulaeman@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'admin',
                'jabatan' => 'Pranata Komputer Ahli Muda',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Tata Usaha,Teknologi Informasi, dan Kepegawaian',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
        admins::create([
                'username' => 'Rianita Kumalasari',
                'nama' => 'Rianita Kumalasari',
                'email' => 'muhammad.hilman@set.wapresri.go.id',
                'password' => Hash::make('SETwapres@2024#'),
                'country' => 'Indonesia',
                'phone' => '081234567890',
                'born' => '2024-11-11',
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta',
                'role' => 'admin',
                'jabatan' => 'Programmer',
                'deputi' => 'Deputi Bidang Administrasi',
                'unit' => 'Biro Tata Usaha,Teknologi Informasi, dan Kepegawaian',
                'created_at' => now(),
                'updated_at' => now(),
        ]);
    }
}
