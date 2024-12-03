<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class indexAdmin extends Controller
{
    public function index()
    {
        // Ambil admin yang login
        $admin = auth()->guard('admin')->user();

        // Daftar kategori sesuai Deputi
        $kategoriDeputi = [
            'deputi_1' => ['Ekonomi dan Keuangan', 'Pekerjaan Umum dan Penataan Ruang', 'Pemulihan Ekonomi Nasional', 'Energi dan SDA', 'Perhubungan', 'Teknologi Informasi dan Komunikasi', 'Perlindungan Konsumen'],
            'deputi_2' => ['Kesehatan', 'Lingkungan Hidup dan Kehutanan', 'Pendidikan dan Kebudayaan', 'Sosial dan Kesejahteraan', 'Ketenagakerjaan', 'Kesetaraan Gender dan Sosial Inklusif', 'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi', 'Kependudukan'],
            'deputi_3' => ['Politisasi ASN', 'Netralitas ASN', 'SP4N Lapor', 'Administrasi Pemerintahan', 'Topik Khusus'],
            'deputi_4' => ['Politik dan Hukum', 'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat', 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika (P4GN)', 'Agama', 'Kekerasan di Satuan Pendidikan', 'Peniadaan Mudik'],
        ];

        // Keywords untuk provinsi
        $provinsiKeywords = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi', 'Sumatera Selatan', 'Bangka Belitung',
            'Bengkulu', 'Lampung', 'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Yogyakarta', 'Jawa Timur', 'Banten',
            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan',
            'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara',
            'Gorontalo', 'Sulawesi Barat', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Tengah', 'Papua Selatan',
            'Papua Pegunungan'
        ];

        // Inisialisasi variabel
        $totalLaporan = $lakiLaki = $perempuan = 0;
        $laporanHarian = collect();
        $provinsiData = collect();
        $judulFrequencies = collect();

        // Periksa role admin
        if ($admin->role === 'admin') {
            // Admin melihat semua data
            $totalLaporan = Laporan::count();
            $lakiLaki = Laporan::where('jenis_kelamin', 'L')->count();
            $perempuan = Laporan::where('jenis_kelamin', 'P')->count();
            $laporanHarian = Laporan::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'ASC')
                ->get();

            $provinsiData = $this->getProvinsiData($provinsiKeywords);
            $judulFrequencies = $this->getJudulFrequencies();
        } else {
            // Deputi melihat data berdasarkan kategori dan disposisi
            $kategori = $kategoriDeputi[$admin->role] ?? [];

            $totalLaporan = Laporan::whereIn('kategori', $kategori)
                ->where('disposisi', $admin->role)
                ->count();

            $lakiLaki = Laporan::where('jenis_kelamin', 'L')
                ->whereIn('kategori', $kategori)
                ->where('disposisi', $admin->role)
                ->count();

            $perempuan = Laporan::where('jenis_kelamin', 'P')
                ->whereIn('kategori', $kategori)
                ->where('disposisi', $admin->role)
                ->count();

            $laporanHarian = Laporan::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
                ->whereIn('kategori', $kategori)
                ->where('disposisi', $admin->role)
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'ASC')
                ->get();

            $provinsiData = $this->getProvinsiData($provinsiKeywords, $kategori, $admin->role);
            $judulFrequencies = $this->getJudulFrequencies($kategori, $admin->role);
        }

        return view('admin.index', [
            'totalLaporan' => $totalLaporan,
            'lakiLaki' => $lakiLaki,
            'perempuan' => $perempuan,
            'laporanHarian' => $laporanHarian,
            'provinsiData' => $provinsiData,
            'judulFrequencies' => $judulFrequencies,
        ]);
    }

    private function getProvinsiData($provinsiKeywords, $kategori = [], $role = null)
    {
        $query = Laporan::selectRaw("
            CASE " . implode(' ', array_map(fn($provinsi) => "WHEN alamat_lengkap LIKE '%$provinsi%' THEN '$provinsi'", $provinsiKeywords)) . "
            ELSE 'Lainnya' END as provinsi, COUNT(*) as total
        ");

        if (!empty($kategori)) {
            $query->whereIn('kategori', $kategori)->where('disposisi', $role);
        }

        return $query->groupBy('provinsi')->get();
    }

    private function getJudulFrequencies($kategori = [], $role = null)
    {
        $query = Laporan::selectRaw('judul, COUNT(*) as total')
            ->groupBy('judul')
            ->orderBy('total', 'desc')
            ->take(5);

        if (!empty($kategori)) {
            $query->whereIn('kategori', $kategori)->where('disposisi', $role);
        }

        return $query->get();
    }

    public function logout()
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            return redirect('/');
        }
    }

}
