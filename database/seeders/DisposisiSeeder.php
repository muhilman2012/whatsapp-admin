<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisposisiSeeder extends Seeder
{
    public function run()
    {
        // Daftar kategori per kedeputian
        $kategoriDeputi = [
            'deputi_1' => ['Ekonomi dan Keuangan', 'Pekerjaan Umum dan Penataan Ruang', 'Pemulihan Ekonomi Nasional', 'Energi dan SDA', 'Perhubungan', 'Teknologi Informasi dan Komunikasi', 'Perlindungan Konsumen'],
            'deputi_2' => ['Kesehatan', 'Lingkungan Hidup dan Kehutanan', 'Pendidikan dan Kebudayaan', 'Sosial dan Kesejahteraan', 'Ketenagakerjaan', 'Kesetaraan Gender dan Sosial Inklusif', 'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi', 'Kependudukan'],
            'deputi_3' => ['Politisasi ASN', 'Netralitas ASN', 'SP4N Lapor', 'Administrasi Pemerintahan', 'Topik Khusus'],
            'deputi_4' => ['Politik dan Hukum', 'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat', 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika (P4GN)', 'Agama', 'Kekerasan di Satuan Pendidikan', 'Peniadaan Mudik'],
        ];

        // Perulangan untuk setiap kategori kedeputian
        foreach ($kategoriDeputi as $deputi => $kategoriList) {
            DB::table('laporans')
                ->whereIn('kategori', $kategoriList) // Filter berdasarkan kategori
                ->update(['disposisi' => $deputi]); // Update disposisi sesuai kedeputian
        }
    }
}
