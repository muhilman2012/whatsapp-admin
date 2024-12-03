<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Laporan;

class DisposisiSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua laporan yang perlu diperbarui
        $laporans = Laporan::all();

        foreach ($laporans as $laporan) {
            // Tentukan kategori dan disposisi
            $result = Laporan::tentukanKategoriDanDeputi($laporan->judul);
            $laporan->update([
                'kategori' => $result['kategori'] ?? 'Lainnya',
                'disposisi' => $result['deputi'] ?? null,
            ]);

            $this->command->info("Laporan ID {$laporan->id} diperbarui: Kategori = {$laporan->kategori}, Disposisi = {$laporan->disposisi}");
        }

        $this->command->info('Proses seeding disposisi selesai!');
    }
}
