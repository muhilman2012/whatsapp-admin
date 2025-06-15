<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Laporan;

class LaporanFollowupController extends Controller
{
    public function show($nomor_tiket)
    {
        try {
            // Ambil detail laporan dari DB lokal
            $laporan = Laporan::where('nomor_tiket', $nomor_tiket)->first();
            $data = Laporan::with('dokumens')->where('nomor_tiket', $nomor_tiket)->firstOrFail();

            if (!$laporan) {
                return back()->with('error', 'Laporan tidak ditemukan.');
            }

            // Konfigurasi API (jika nanti disimpan di DB, bisa ditarik dari model/setting)
            $baseUrl = 'https://api-splp.layanan.go.id/sandbox-konsolidasi/1.0';
            $headers = [
                'auth' => 'Bearer $2y$10$Pz9SSCKbT0lQOWJXcVS66epn80Cboz1ZTzBlkZmomD87/qe2BAnKa',
                'token' => '{ITVMCLDK-WCJL-L5O5-3HES-NUJYIJVX6AZ6}',
            ];

            // Ambil semua halaman tindak lanjut
            $page = 1;
            $allFollowups = [];

            do {
                $response = Http::withHeaders($headers)->get("{$baseUrl}/complaints/{$nomor_tiket}/followups?page={$page}");

                if (!$response->successful()) {
                    return back()->with('error', 'Gagal mengambil data tindak lanjut.');
                }

                $result = $response->json('results');

                $allFollowups = array_merge($allFollowups, $result['data']);
                $nextPage = $result['next_page_url'] ?? null;
                $page++;
            } while ($nextPage);

            return view('admin.laporan.followup', [
                'followups' => $allFollowups,
                'nomor_tiket' => $nomor_tiket,
                'laporan' => $laporan,
                'data'  => $data,
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
