<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Assignment;
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

        // Mapping nama lengkap deputi ke singkatan
        $deputiMapping = [
            'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata dan Transformasi Digital' => 'deputi_1',
            'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia' => 'deputi_2',
            'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan' => 'deputi_3',
            'Deputi Bidang Administrasi' => 'deputi_4',
        ];
        
        // Ambil nama deputi dari kolom deputi di tabel admins
        $deputiName = $admin->deputi; // Misalkan kolom 'deputi' di tabel admins berisi nama lengkap

        // Dapatkan singkatan deputi berdasarkan nama lengkap
        $deputiRole = array_search($deputiName, $deputiMapping);

        // Ambil kategori yang relevan untuk role asdep
        $kategoriByUnit = Laporan::getKategoriByUnit($admin->unit);

        // Ambil daftar kategori sesuai Deputi menggunakan getter
        $kategoriDeputi = Laporan::getKategoriDeputi();

        // Tentukan kategori yang bisa diakses menggunakan getter
        $kategoriKataKunci = Laporan::getKategoriKataKunci();
        
        // Modifikasi logika untuk menentukan kategori berdasarkan role
        $kategori = in_array($admin->role, ['superadmin', 'admin'])
            ? array_keys($kategoriKataKunci) // Semua kategori untuk superadmin dan admin
            : ($admin->role === 'asdep' ? Laporan::getKategoriByUnit($admin->unit) : ($kategoriDeputi[$admin->role] ?? [])); // Kategori sesuai role Deputi atau asdep

        // Hitung total laporan
        $totalLaporanQuery = Laporan::query();

        // Jika pengguna adalah superadmin atau admin, hitung semua laporan
        if (in_array($admin->role, ['superadmin', 'admin'])) {
            $totalLaporan = $totalLaporanQuery->count();
        } elseif ($admin->role === 'analis') {
            // Jika pengguna adalah analis, hitung laporan yang di-assign ke mereka
            $totalLaporan = $totalLaporanQuery->whereHas('assignment', function ($query) use ($admin) {
                $query->where('analis_id', $admin->id_admins); // Filter berdasarkan analis yang login
            })->count();
        } elseif ($admin->role === 'asdep') {
            // Jika pengguna adalah asdep, hitung laporan berdasarkan kategori unit
            $kategoriByUnit = Laporan::getKategoriByUnit($admin->unit);
            $totalLaporan = $totalLaporanQuery->whereIn('kategori', $kategoriByUnit)->count();
        } else {
            // Jika pengguna adalah deputi atau role lainnya
            $totalLaporan = $totalLaporanQuery->where(function ($query) use ($admin) {
                $query->where('disposisi', $admin->role)
                    ->orWhere('disposisi_terbaru', $admin->role); // Tambahkan kondisi untuk disposisi_terbaru
            })->count();
        }

        // Hitung laporan whatsapp dan tatap muka
        $whatsappQuery = clone $totalLaporanQuery;
        $whatsapp = $whatsappQuery->where('sumber_pengaduan', 'whatsapp')->count();

        $tatapMukaQuery = clone $totalLaporanQuery;
        $tatapMuka = $tatapMukaQuery->where('sumber_pengaduan', 'tatap muka')->count();

        // Hitung laporan laki-laki dan perempuan
        $lakiLakiQuery = clone $totalLaporanQuery;
        $lakiLaki = $lakiLakiQuery->where('jenis_kelamin', 'L')->count();

        $perempuanQuery = clone $totalLaporanQuery;
        $perempuan = $perempuanQuery->where('jenis_kelamin', 'P')->count();

        // Total laporan yang terdisposisi
        $totalTerdisposisi = Laporan::whereNotNull('disposisi')->count();
        $belumTerdisposisi = Laporan::whereNull('disposisi')  
            ->whereNull('disposisi_terbaru')  
            ->count();

        $deputi1 = Laporan::where(function ($query) {
            $query->where('disposisi', 'deputi_1')
                  ->orWhere('disposisi_terbaru', 'deputi_1');
        })->count();
        
        $deputi2 = Laporan::where(function ($query) {
            $query->where('disposisi', 'deputi_2')
                  ->orWhere('disposisi_terbaru', 'deputi_2');
        })->count();
        
        $deputi3 = Laporan::where(function ($query) {
            $query->where('disposisi', 'deputi_3')
                  ->orWhere('disposisi_terbaru', 'deputi_3');
        })->count();
        
        $deputi4 = Laporan::where(function ($query) {
            $query->where('disposisi', 'deputi_4')
                  ->orWhere('disposisi_terbaru', 'deputi_4');
        })->count();
        
        // Total laporan berdasarkan role
        $totalLaporan = $totalLaporanQuery->count(); // Total laporan untuk role tertentu

        // Hitung jumlah laporan yang sudah di-assign
        $totalAssignedToAnalis = $totalLaporanQuery->whereIn('id', function ($query) use ($admin) {
            $query->select('laporan_id')
                ->from('assignments')
                ->when($admin->role === 'asdep', function ($subQuery) use ($admin) {
                    $subQuery->whereIn('laporan_id', function ($innerQuery) use ($admin) {
                        $innerQuery->select('id')
                            ->from('laporans')
                            ->whereIn('kategori', Laporan::getKategoriByUnit($admin->unit));
                    });
                })
                ->when($admin->role !== 'asdep' && $admin->role !== 'superadmin' && $admin->role !== 'admin', function ($subQuery) use ($admin) {
                    $subQuery->whereIn('laporan_id', function ($innerQuery) use ($admin) {
                        $innerQuery->select('id')
                            ->from('laporans')
                            ->where('disposisi', $admin->role);
                    });
                });
        })->count(); // Pastikan ini adalah count() untuk mendapatkan integer

        // Hitung total laporan yang belum di-assign
        $totalNotAssigned = $totalLaporan - $totalAssignedToAnalis; // Ini sekarang harus berfungsi

        // Definisikan status secara eksplisit
        $allStatuses = [
            'Belum dapat diproses lebih lanjut',
            'Dalam pemantauan terhadap penanganan yang sedang dilakukan oleh instansi berwenang',
            'Disampaikan kepada Pimpinan K/L untuk penanganan lebih lanjut',
            'Proses verifikasi dan telaah'
        ];

        // Pastikan status data juga diambil dengan benar
        $statusData = Laporan::selectRaw('status, COUNT(*) as total')
            ->when($admin->role === 'asdep', function ($query) use ($kategoriByUnit) {
                $query->whereIn('kategori', $kategoriByUnit); // Filter berdasarkan kategori
            })
            ->when(!in_array($admin->role, ['superadmin', 'admin', 'asdep']), function ($query) use ($admin) {
                $query->where('disposisi', $admin->role); // Filter data berdasarkan disposisi jika bukan superadmin, admin, atau asdep
            })
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Label singkat untuk status
        $shortLabels = [
            'Belum dapat diproses lebih lanjut' => 'Tidak Diproses',
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

        // Query untuk laporan harian
        $laporanHarian = Laporan::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->when($admin->role === 'asdep', function ($query) use ($kategoriByUnit) {
                $query->whereIn('kategori', $kategoriByUnit); // Filter berdasarkan kategori
            })
            ->when(!in_array($admin->role, ['superadmin', 'admin', 'asdep']), function ($query) use ($admin) {
                $query->where('disposisi', $admin->role); // Filter data berdasarkan disposisi jika bukan superadmin, admin, atau asdep
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
            'whatsapp' => $whatsapp,
            'tatapMuka' => $tatapMuka,
            'lakiLaki' => $lakiLaki,
            'perempuan' => $perempuan,
            'totalTerdisposisi' => $totalTerdisposisi,
            'belumTerdisposisi' => $belumTerdisposisi,
            'totalAssignedToAnalis' => $totalAssignedToAnalis,
            'totalNotAssigned' => $totalNotAssigned,
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

        // Memperbolehkan akses untuk superadmin dan admin
        if (!empty($kategori) && !in_array($role, ['superadmin', 'admin'])) {
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

        // Memperbolehkan akses untuk superadmin dan admin
        if (!empty($kategori) && !in_array($role, ['superadmin', 'admin'])) {
            $query->whereIn('kategori', $kategori)->where('disposisi', $role);
        }

        return $query->get();
    }

    private function getLaporanPerKategori($kategori, $role)
    {
        $query = Laporan::selectRaw('kategori, COUNT(*) as total');

        // Memperbolehkan akses untuk superadmin dan admin
        if (!empty($kategori) && !in_array($role, ['superadmin', 'admin'])) {
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
