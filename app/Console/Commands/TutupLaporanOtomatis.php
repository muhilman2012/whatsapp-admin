<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Laporan;
use Carbon\Carbon;
use App\Models\Log;

class TutupLaporanOtomatis extends Command
{
    protected $signature = 'laporan:tutup-otomatis';
    protected $description = 'Menutup laporan yang tidak melengkapi dokumen tambahan dalam 10 hari setelah status "Menunggu kelengkapan data dukung dari Pelapor"';

    public function handle()
    {
        $laporans = Laporan::where('status', 'Menunggu kelengkapan data dukung dari Pelapor')
            ->whereNull('dokumen_tambahan')
            ->where('updated_at', '<', Carbon::now()->subDays(14))
            ->get();

        $jumlahLaporanDitutup = $laporans->count();

        if ($laporans->isEmpty()) {
            $this->info('Tidak ada laporan yang perlu ditutup.');
            return;
        }

        foreach ($laporans as $laporan) {
            // Simpan log aktivitas
            Log::create([
                'laporan_id' => $laporan->id,
                'activity' => "Laporan diarsipkan otomatis karena pengadu tidak memberikan kelengkapan data dalam 10 hari.",
                'user_id' => 1,
            ]);

            $laporan->status = 'Penanganan Selesai';
            $laporan->tanggapan = 'Pengaduan diarsipkan karena pengadu dalam jangka waktu 10 (sepuluh) hari tidak memberikan kelengkapan data.';
            $laporan->save();
        }

        // Tampilkan jumlah laporan yang berhasil ditutup
        $this->info("Proses penutupan laporan otomatis selesai. Total laporan yang ditutup: $jumlahLaporanDitutup");
    }
}