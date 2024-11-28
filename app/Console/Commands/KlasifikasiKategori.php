<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Laporan;

class KlasifikasiKategori extends Command
{
    protected $signature = 'laporan:klasifikasi-kategori';
    protected $description = 'Mengklasifikasikan kategori laporan berdasarkan judul';

    public function handle()
    {
        $laporans = Laporan::whereNull('kategori')->get();

        foreach ($laporans as $laporan) {
            $laporan->kategori = Laporan::tentukanKategori($laporan->judul);
            $laporan->save();
            $this->info("Laporan ID {$laporan->id} dikategorikan sebagai {$laporan->kategori}");
        }

        $this->info('Proses klasifikasi selesai!');
    }
}
