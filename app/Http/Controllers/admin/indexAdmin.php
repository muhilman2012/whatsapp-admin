<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class indexAdmin extends Controller
{
    // show dashboard
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

        // Periksa apakah user adalah admin
        if ($admin->role === 'admin') {
            // Admin bisa melihat semua data
            $totalLaporan = Laporan::count();
            $lakiLaki = Laporan::where('jenis_kelamin', 'L')->count();
            $perempuan = Laporan::where('jenis_kelamin', 'P')->count();
            $laporanHarian = Laporan::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'ASC')
                ->get();
        } else {
            // Jika Deputi, filter berdasarkan disposisi dan kategori
            $kategori = $kategoriDeputi[$admin->role] ?? []; // Kategori sesuai role Deputi

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
        }

        return view('admin.index', [
            'totalLaporan' => $totalLaporan,
            'lakiLaki' => $lakiLaki,
            'perempuan' => $perempuan,
            'laporanHarian' => $laporanHarian,
        ]);
    }

    public function logout()
    {
        if(Auth::guard('admin')->check()){
            Auth::guard('admin')->logout();
            return redirect('/');
        }
    }

}
