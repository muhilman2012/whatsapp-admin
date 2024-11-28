<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class ExportController extends Controller
{
    public function exportByDate(Request $request)
    {
        $userRole = auth()->guard('admin')->user()->role; // Ambil role admin

        // Daftar kategori yang sesuai untuk Deputi
        $kategoriDeputi = [
            'deputi_1' => ['Ekonomi dan Keuangan', 'Pekerjaan Umum dan Penataan Ruang', 'Pemulihan Ekonomi Nasional', 'Energi dan Sumber Daya Alam', 'Perhubungan', 'Teknologi Informasi dan Komunikasi', 'Perlindungan Konsumen'],
            'deputi_2' => ['Kesehatan', 'Pendidikan dan Kebudayaan', 'Sosial dan Kesejahteraan', 'Pembangunan Desa, Daerah Tetinggal, dan Transmigrasi', 'Kesetaraan Gender dan Sosial Inklusif', 'Ketenagakerjaan', 'Kependudukan'],
            'deputi_3' => ['Politisasi ASN', 'Netralitas ASN', 'Administrasi Pemerintahan', 'Dukungan Sistem Pengelolaan', 'SP4N Lapor', 'Topik Khusus'],
            'deputi_4' => ['Politik dan Hukum', 'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat', 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika (P4GN)', 'Lingkungan Hidup dan Kehutanan', 'Agama', 'Kekerasan di Satuan Pendidikan', 'Peniadaan Mudik'],
        ];

        // Ambil kategori sesuai role
        $kategori = $kategoriDeputi[$userRole] ?? [];

        // Validasi input tanggal
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        // Ambil data sesuai kategori dan tanggal
        $tanggal = $request->tanggal;
        $data = Laporan::whereDate('created_at', $tanggal)
            ->whereIn('kategori', $kategori) // Filter kategori berdasarkan Deputi
            ->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pada tanggal tersebut.');
        }

        // Export data ke Excel
        return Excel::download(new LaporanExport($data), 'laporan_' . $tanggal . '.xlsx');
    }
}
