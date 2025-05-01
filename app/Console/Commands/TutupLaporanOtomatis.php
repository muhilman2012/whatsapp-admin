<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Laporan;
use Carbon\Carbon;
use App\Models\Log;

class TutupLaporanOtomatis extends Command
{
    protected $signature = 'laporan:tutup-otomatis';
    protected $description = 'Menutup laporan jika status Menunggu kelengkapan tidak dilengkapi selama 10 hari kerja.';

    public function handle()
    {
        $this->info("Menjalankan command pada " . now());

        $laporans = Laporan::where('status', 'Menunggu kelengkapan data dukung dari Pelapor')
            ->where(function ($q) {
                $q->whereNull('dokumen_tambahan')
                  ->orWhere('dokumen_tambahan', '')
                  ->orWhere('dokumen_tambahan', '[]');
            })
            ->where('updated_at', '<', Carbon::now()->subDays(12))
            ->get();

        $jumlah = $laporans->count();

        if ($jumlah === 0) {
            $this->info('Tidak ada laporan yang perlu ditutup.');
            return;
        }

        foreach ($laporans as $laporan) {
            Log::create([
                'laporan_id' => $laporan->id,
                'activity' => "Laporan diarsipkan otomatis karena pengadu tidak memberikan kelengkapan data dalam 10 hari kerja.",
                'user_id' => 1,
            ]);

            $laporan->status = 'Penanganan Selesai';
            $laporan->tanggapan = 'Pengaduan diarsipkan karena pengadu tidak memberikan kelengkapan data dalam waktu 10 hari kerja.';
            $laporan->save();
        }

        $this->info("Selesai. Total laporan yang ditutup: $jumlah");
    }
}