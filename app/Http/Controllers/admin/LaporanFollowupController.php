<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Laporan;
use App\Models\ApiSetting;

class LaporanFollowupController extends Controller
{
    public function show($nomor_tiket)
    {
        try {
            // Ambil detail laporan berdasarkan nomor_tiket
            $laporan = Laporan::where('nomor_tiket', $nomor_tiket)->first();
            $data = Laporan::with('dokumens')->where('nomor_tiket', $nomor_tiket)->firstOrFail();

            if (!$laporan) {
                return back()->with('error', 'Laporan tidak ditemukan.');
            }

            // Pastikan complaint_id tersedia di tabel Laporan
            $complaintId = $laporan->complaint_id;
            if (!$complaintId) {
                return back()->with('error', 'Complaint ID tidak ditemukan untuk laporan ini.');
            }

            // Ambil konfigurasi dari tabel api_settings
            $baseUrl = $this->getApiSetting('base_url');
            $headers = [
                'auth' => $this->getApiSetting('auth'),
                'token' => $this->getApiSetting('token'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            // Ambil semua data tindak lanjut berdasarkan complaint_id
            $page = 1;
            $allFollowups = [];

            do {
                $response = Http::withHeaders($headers)
                    ->get("{$baseUrl}/complaints/{$complaintId}/followups?page={$page}");

                if (!$response->successful()) {
                    return back()->with('error', 'Gagal mengambil data tindak lanjut dari API.');
                }

                $result = $response->json('results');
                $allFollowups = array_merge($allFollowups, $result['data'] ?? []);
                $nextPage = $result['next_page_url'] ?? null;
                $page++;
            } while ($nextPage);

            return view('admin.laporan.followup', [
                'followups' => $allFollowups,
                'nomor_tiket' => $nomor_tiket,
                'laporan' => $laporan,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Ambil nilai konfigurasi dari tabel api_settings berdasarkan key
     */
    private function getApiSetting($key)
    {
        return ApiSetting::where('key', $key)->value('value') ?? '';
    }
}
