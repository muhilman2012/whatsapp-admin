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
        // Ambil admin yang sedang login
        $admin = auth()->guard('admin')->user();

        // Ambil daftar kategori sesuai Deputi menggunakan getter
        $kategoriDeputi = Laporan::getKategoriDeputi();

        // Tentukan kategori yang bisa diakses menggunakan getter
        $kategoriKataKunci = Laporan::getKategoriKataKunci();
        $kategori = $admin->role === 'admin'
            ? array_keys($kategoriKataKunci) // Semua kategori untuk admin
            : ($kategoriDeputi[$admin->role] ?? []); // Kategori sesuai role Deputi

        // Hitung total laporan
        $totalLaporanQuery = Laporan::query();
        if ($admin->role !== 'admin') {
            $totalLaporanQuery->whereIn('kategori', $kategori)->where('disposisi', $admin->role);
        }
        $totalLaporan = $totalLaporanQuery->count();

        // Hitung laporan laki-laki dan perempuan
        $lakiLakiQuery = clone $totalLaporanQuery;
        $lakiLaki = $lakiLakiQuery->where('jenis_kelamin', 'L')->count();

        $perempuanQuery = clone $totalLaporanQuery;
        $perempuan = $perempuanQuery->where('jenis_kelamin', 'P')->count();

        // Total laporan yang terdisposisi
        $totalTerdisposisi = Laporan::whereNotNull('disposisi')->count();
        $belumTerdisposisi = Laporan::whereNull('disposisi')->count();

        $deputi1 = Laporan::where('disposisi', 'deputi_1')->count();
        $deputi2 = Laporan::where('disposisi', 'deputi_2')->count();
        $deputi3 = Laporan::where('disposisi', 'deputi_3')->count();
        $deputi4 = Laporan::where('disposisi', 'deputi_4')->count();

        // Definisikan status secara eksplisit
        $allStatuses = [
            'Tidak dapat diproses lebih lanjut',
            'Dalam pemantauan terhadap penanganan yang sedang dilakukan oleh instansi berwenang',
            'Disampaikan kepada Pimpinan K/L untuk penanganan lebih lanjut',
            'Proses verifikasi dan telaah'
        ];

        // Ambil data status berdasarkan role
        $statusData = Laporan::selectRaw('status, COUNT(*) as total')
            ->when($admin->role !== 'admin', function ($query) use ($admin) {
                $query->where('disposisi', $admin->role); // Filter data berdasarkan disposisi jika bukan admin
            })
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Label singkat untuk status
        $shortLabels = [
            'Tidak dapat diproses lebih lanjut' => 'Tidak Diproses',
            'Dalam pemantauan terhadap penanganan yang sedang dilakukan oleh instansi berwenang' => 'Pemantauan',
            'Disampaikan kepada Pimpinan K/L untuk penanganan lebih lanjut' => 'Tindak Lanjut K/L',
            'Proses verifikasi dan telaah' => 'Verifikasi'
        ];

        // Pastikan semua status muncul, meskipun datanya kosong
        $statusCounts = [];
        foreach ($allStatuses as $status) {
            $statusCounts[$status] = $statusData[$status]->total ?? 0;
        }

        // Format data untuk chart
        $chartData = [];
        foreach ($statusCounts as $status => $count) {
            $chartData[] = [
                'label' => "{$shortLabels[$status]} = {$count}",
                'value' => $count
            ];
        }

        // Data Laporan Harian
        $laporanHarian = Laporan::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
        ->when($admin->role !== 'admin', function ($query) use ($admin) {
            $query->where('disposisi', $admin->role); // Filter data berdasarkan disposisi jika bukan admin
        })
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'ASC')
        ->get();

        // Data laporan per provinsi
        $provinsiKeywords = $this->getProvinsiKeywords();
        $provinsiData = $this->getProvinsiData($provinsiKeywords, $kategori, $admin->role);

        // Frekuensi judul laporan
        $judulFrequencies = $this->getJudulFrequencies($kategori, $admin->role);

        // Ambil laporan per kategori
        $laporanPerKategori = Laporan::selectRaw('kategori, COUNT(*) as total')
        ->whereIn('kategori', $kategori)
        ->groupBy('kategori')
        ->orderBy('total', 'desc') // Urutkan berdasarkan jumlah laporan terbanyak
        ->get();

        // Pisahkan kategori 'Lainnya' dan tempatkan di akhir
        $laporanPerKategori = $laporanPerKategori->partition(function ($item) {
            return $item->kategori !== 'Lainnya';
        })->flatten();

        return view('admin.index', [
            'totalLaporan' => $totalLaporan,
            'lakiLaki' => $lakiLaki,
            'perempuan' => $perempuan,
            'totalTerdisposisi' => $totalTerdisposisi,
            'belumTerdisposisi' => $belumTerdisposisi,
            'deputi1' => $deputi1,
            'deputi2' => $deputi2,
            'deputi3' => $deputi3,
            'deputi4' => $deputi4,
            'laporanHarian' => $laporanHarian,
            'provinsiData' => $provinsiData,
            'judulFrequencies' => $judulFrequencies,
            'laporanPerKategori' => $laporanPerKategori,
            'chartData' => $chartData
        ]);
    }

    private function getProvinsiKeywords()
    {
        return [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi',
            'Sumatera Selatan', 'Bangka Belitung', 'Bengkulu', 'Lampung', 'DKI Jakarta',
            'Jawa Barat', 'Jawa Tengah', 'Yogyakarta', 'Jawa Timur', 'Banten', 'Bali',
            'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah',
            'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara',
            'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo',
            'Sulawesi Barat', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat',
            'Papua Tengah', 'Papua Selatan', 'Papua Pegunungan'
        ];
    }

    private function getProvinsiData($provinsiKeywords, $kategori, $role)
    {
        $query = Laporan::selectRaw("
            CASE " . implode(' ', array_map(fn($provinsi) => "WHEN alamat_lengkap LIKE '%$provinsi%' THEN '$provinsi'", $provinsiKeywords)) . "
            ELSE 'Lainnya' END as provinsi, COUNT(*) as total
        ");

        if (!empty($kategori) && $role !== 'admin') {
            $query->whereIn('kategori', $kategori)->where('disposisi', $role);
        }

        return $query->groupBy('provinsi')->get();
    }

    private function getJudulFrequencies($kategori, $role)
    {
        $query = Laporan::selectRaw('judul, COUNT(*) as total')
            ->groupBy('judul')
            ->orderBy('total', 'desc')
            ->take(5);

        if (!empty($kategori) && $role !== 'admin') {
            $query->whereIn('kategori', $kategori)->where('disposisi', $role);
        }

        return $query->get();
    }

    private function getLaporanPerKategori($kategori, $role)
    {
        $query = Laporan::selectRaw('kategori, COUNT(*) as total');

        if (!empty($kategori) && $role !== 'admin') {
            $query->whereIn('kategori', $kategori)->where('disposisi', $role);
        }

        return $query->groupBy('kategori')->get();
    }

    public function logout()
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            return redirect('/');
        }
    }
}
