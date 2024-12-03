<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Laporan;

class KlasifikasiKategori extends Command
{
    protected $signature = 'laporan:klasifikasi-kategori';
    protected $description = 'Mengklasifikasikan kategori dan disposisi laporan berdasarkan judul';

    public function handle()
    {
        $laporans = Laporan::whereNull('kategori')->orWhereNull('disposisi')->get();

        foreach ($laporans as $laporan) {
            // Tentukan kategori dan disposisi
            $result = Laporan::tentukanKategoriDanDeputi($laporan->judul);
            $laporan->kategori = $result['kategori'] ?? 'Lainnya';
            $laporan->disposisi = $result['deputi'] ?? null;
            $laporan->save();

            $this->info("Laporan ID {$laporan->id} dikategorikan sebagai {$laporan->kategori} dan didisposisikan ke {$laporan->disposisi}");
        }

        $this->info('Proses klasifikasi kategori dan disposisi selesai!');
    }
}
