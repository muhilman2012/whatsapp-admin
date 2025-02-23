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

        if (in_array($admin->role, ['superadmin', 'admin'])) {
            // Jika pengguna adalah superadmin atau admin, hitung semua laporan
            $totalLaporan = $totalLaporanQuery->count();
        } elseif ($admin->role === 'analis') {
            // Jika pengguna adalah analis, hitung laporan yang di-assign ke mereka
            $totalLaporan = $totalLaporanQuery->whereHas('assignments', function ($query) use ($admin) {
                $query->where('analis_id', $admin->id_admins);
            })->count();
        } elseif ($admin->role === 'asdep') {
            // Jika pengguna adalah asdep, hitung laporan berdasarkan kategori unit
            $kategoriByUnit = Laporan::getKategoriByUnit($admin->unit);
            $totalLaporan = $totalLaporanQuery->whereIn('kategori', $kategoriByUnit)->count();
        } else {
            // Jika pengguna adalah deputi
            $totalLaporan = $totalLaporanQuery->where(function ($query) use ($admin) {
                $query->where('disposisi', $admin->role)->whereNull('disposisi_terbaru')
                    ->orWhere('disposisi_terbaru', $admin->role);
            })->count();
        }

        // Query untuk whatsapp dan tatap muka harus memperhatikan disposisi dan disposisi_terbaru
        $whatsappQuery = clone $totalLaporanQuery;
        $whatsapp = $whatsappQuery->where('sumber_pengaduan', 'whatsapp')->count();

        $tatapMukaQuery = clone $totalLaporanQuery;
        $tatapMuka = $tatapMukaQuery->where('sumber_pengaduan', 'tatap muka')->count();

        // Query untuk laporan laki-laki dan perempuan
        $lakiLakiQuery = clone $totalLaporanQuery;
        $lakiLaki = $lakiLakiQuery->where('jenis_kelamin', 'L')->count();

        $perempuanQuery = clone $totalLaporanQuery;
        $perempuan = $perempuanQuery->where('jenis_kelamin', 'P')->count();

        // Total laporan yang terdisposisi
        $totalTerdisposisi = Laporan::whereNotNull('disposisi')
            ->orWhereNotNull('disposisi_terbaru')
            ->count();
        $belumTerdisposisi = Laporan::whereNull('disposisi')  
            ->whereNull('disposisi_terbaru')  
            ->count();

        // Hitung total laporan untuk setiap deputi berdasarkan sumber pengaduan
        $deputiRoles = ['deputi_1', 'deputi_2', 'deputi_3', 'deputi_4'];
        foreach ($deputiRoles as $deputi) {
            ${"{$deputi}WhatsApp"} = Laporan::where(function ($query) use ($deputi) {
                $query->where(function ($q) use ($deputi) {
                    $q->where('disposisi', $deputi)->whereNull('disposisi_terbaru');
                })->orWhere(function ($q) use ($deputi) {
                    $q->where('disposisi_terbaru', $deputi);
                });
            })->where('sumber_pengaduan', 'whatsapp')->count();

            ${"{$deputi}TatapMuka"} = Laporan::where(function ($query) use ($deputi) {
                $query->where(function ($q) use ($deputi) {
                    $q->where('disposisi', $deputi)->whereNull('disposisi_terbaru');
                })->orWhere(function ($q) use ($deputi) {
                    $q->where('disposisi_terbaru', $deputi);
                });
            })->where('sumber_pengaduan', 'tatap muka')->count();
        }
        
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

        // Definisi status dengan label pendek
        $shortLabels = [
            'Penanganan Selesai' => 'Selesai',
            'Menunggu kelengkapan data dukung dari Pelapor' => 'Kelengkapan',
            'Diteruskan kepada instansi yang berwenang untuk penanganan lebih lanjut' => 'Tindak Lanjut K/L',
            'Proses verifikasi dan telaah' => 'Verifikasi'
        ];

        // Daftar kedeputian yang valid
        $deputiRoles = ['deputi_1', 'deputi_2', 'deputi_3', 'deputi_4'];

        // Query dasar untuk filter role
        $queryBase = Laporan::query();
        if ($admin->role === 'asdep') {
            $queryBase->whereIn('kategori', $kategoriByUnit);
        } elseif (!in_array($admin->role, ['superadmin', 'admin', 'asdep'])) {
            $queryBase->where(function ($query) use ($admin) {
                $query->where('disposisi', $admin->role)
                    ->orWhere('disposisi_terbaru', $admin->role);
            });
        }

        // Ambil jumlah total laporan berdasarkan status
        $statusData = (clone $queryBase)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Ambil jumlah laporan berdasarkan status & sumber_pengaduan
        $statusBySource = (clone $queryBase)
            ->selectRaw('status, sumber_pengaduan, COUNT(*) as total')
            ->groupBy('status', 'sumber_pengaduan')
            ->get()
            ->groupBy('status');

        $chartData = [];

        foreach ($shortLabels as $fullStatus => $shortLabel) {
            $totalStatus = isset($statusData[$fullStatus]) ? $statusData[$fullStatus]->total : 0;

            $whatsappCount = isset($statusBySource[$fullStatus])
                ? ($statusBySource[$fullStatus]->where('sumber_pengaduan', 'whatsapp')->first()->total ?? 0)
                : 0;

            $tatapMukaCount = isset($statusBySource[$fullStatus])
                ? ($statusBySource[$fullStatus]->where('sumber_pengaduan', 'tatap muka')->first()->total ?? 0)
                : 0;

            $chartData[] = [
                'label' => "{$shortLabel} = {$totalStatus}",
                'value' => $totalStatus,
                'whatsapp' => $whatsappCount,
                'tatap_muka' => $tatapMukaCount
            ];
        }

        // **Perhitungan Data untuk Tiap Deputi dengan Breakdown WA/TM**
        $chartDataDeputi = [];

        foreach ($deputiRoles as $deputi) {
            // Ambil jumlah laporan berdasarkan status dan sumber_pengaduan hanya untuk deputi terkait
            $deputiStatuses = Laporan::selectRaw('status, sumber_pengaduan, COUNT(*) as total')
                ->where(function ($query) use ($deputi, $deputiRoles) {
                    $query->where('disposisi', $deputi)
                        ->whereNull('disposisi_terbaru'); // Jika tidak ada disposisi terbaru
                })
                ->orWhere(function ($query) use ($deputi, $deputiRoles) {
                    $query->where('disposisi_terbaru', $deputi)
                        ->whereIn('disposisi_terbaru', $deputiRoles); // Hanya hitung jika disposisi terbaru masih deputi
                })
                ->groupBy('status', 'sumber_pengaduan')
                ->get()
                ->groupBy('status');

            // Pastikan semua status ada, meskipun nilainya 0
            $chartDataDeputi[$deputi] = [];
            foreach ($shortLabels as $fullStatus => $shortLabel) {
                // Total laporan untuk status ini
                $totalStatus = isset($deputiStatuses[$fullStatus]) ? $deputiStatuses[$fullStatus]->sum('total') : 0;

                // Breakdown jumlah laporan berdasarkan sumber_pengaduan
                $whatsappCount = isset($deputiStatuses[$fullStatus]) 
                    ? ($deputiStatuses[$fullStatus]->where('sumber_pengaduan', 'whatsapp')->sum('total') ?? 0) 
                    : 0;

                $tatapMukaCount = isset($deputiStatuses[$fullStatus]) 
                    ? ($deputiStatuses[$fullStatus]->where('sumber_pengaduan', 'tatap muka')->sum('total') ?? 0) 
                    : 0;

                // Tambahkan data untuk chart
                $chartDataDeputi[$deputi][] = [
                    'label' => "{$shortLabel} = {$totalStatus}",
                    'value' => $totalStatus,
                    'whatsapp' => $whatsappCount,
                    'tatap_muka' => $tatapMukaCount
                ];
            }
        }

        // Query untuk laporan harian  
        $laporanHarian = Laporan::selectRaw('DATE(created_at) as tanggal,   
            SUM(CASE WHEN sumber_pengaduan = "whatsapp" THEN 1 ELSE 0 END) as total_whatsapp,   
            SUM(CASE WHEN sumber_pengaduan = "tatap muka" THEN 1 ELSE 0 END) as total_tatap_muka')  
            ->when($admin->role === 'asdep', function ($query) use ($kategoriByUnit) {  
                $query->whereIn('kategori', $kategoriByUnit); // Filter berdasarkan kategori  
            })  
            ->when($admin->role === 'superadmin' || $admin->role === 'admin', function ($query) {
                // Tidak perlu filter untuk superadmin atau admin
            })  
            ->when(!in_array($admin->role, ['superadmin', 'admin', 'asdep']), function ($query) use ($admin) {  
                $query->where(function($q) use ($admin) {
                    $q->where('disposisi', $admin->role)
                    ->whereNull('disposisi_terbaru'); // Hanya hitung jika tidak ada disposisi terbaru
                })->orWhere(function($q) use ($admin) {
                    $q->where('disposisi_terbaru', $admin->role); // Hitung jika disposisi terbaru sesuai dengan role
                });
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
            'deputi_1WhatsApp' => $deputi_1WhatsApp,
            'deputi_1TatapMuka' => $deputi_1TatapMuka,
            'deputi_2WhatsApp' => $deputi_2WhatsApp,
            'deputi_2TatapMuka' => $deputi_2TatapMuka,
            'deputi_3WhatsApp' => $deputi_3WhatsApp,
            'deputi_3TatapMuka' => $deputi_3TatapMuka,
            'deputi_4WhatsApp' => $deputi_4WhatsApp,
            'deputi_4TatapMuka' => $deputi_4TatapMuka,
            'laporanHarian' => $laporanHarian,
            'provinsiData' => $provinsiData,
            'judulFrequencies' => $judulFrequencies,
            'laporanPerKategori' => $laporanPerKategori,
            'chartData' => $chartData,
            'chartDataDeputi' => $chartDataDeputi
        ]);
    }

    private function getProvinsiKeywords()
    {
        return [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi',
            'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung', 'DKI Jakarta',
            'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 'Bali',
            'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah',
            'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara',
            'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo',
            'Sulawesi Barat', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat/Barat Daya',
            'Papua Tengah', 'Papua Selatan', 'Papua Pegunungan'
        ];
    }

    private function getNikToProvinsiMapping()
    {
        return [
            '11' => 'Aceh',
            '12' => 'Sumatera Utara',
            '13' => 'Sumatera Barat',
            '14' => 'Riau',
            '15' => 'Jambi',
            '16' => 'Sumatera Selatan',
            '17' => 'Bengkulu',
            '18' => 'Lampung',
            '19' => 'Kepulauan Bangka Belitung',
            '21' => 'Kepulauan Riau',
            '31' => 'DKI Jakarta',
            '32' => 'Jawa Barat',
            '33' => 'Jawa Tengah',
            '34' => 'DI Yogyakarta',
            '35' => 'Jawa Timur',
            '36' => 'Banten',
            '51' => 'Bali',
            '52' => 'Nusa Tenggara Barat',
            '53' => 'Nusa Tenggara Timur',
            '61' => 'Kalimantan Barat',
            '62' => 'Kalimantan Tengah',
            '63' => 'Kalimantan Selatan',
            '64' => 'Kalimantan Timur',
            '65' => 'Kalimantan Utara',
            '71' => 'Sulawesi Utara',
            '72' => 'Sulawesi Tengah',
            '73' => 'Sulawesi Selatan',
            '74' => 'Sulawesi Tenggara',
            '75' => 'Gorontalo',
            '76' => 'Sulawesi Barat',
            '81' => 'Maluku',
            '82' => 'Maluku Utara',
            '91' => 'Papua',
            '92' => 'Papua Barat/Barat Daya',
            '93' => 'Papua Selatan',
            '94' => 'Papua Tengah',
            '95' => 'Papua Pegunungan'
        ];
    }

    private function getProvinsiData()
    {
        $nikToProvinsiMapping = $this->getNikToProvinsiMapping();

        // Membuat kondisi CASE SQL berdasarkan mapping nik ke provinsi
        $caseStatement = "CASE ";
        foreach ($nikToProvinsiMapping as $nik => $provinsi) {
            $caseStatement .= "WHEN LEFT(nik, 2) = '$nik' THEN '$provinsi' ";
        }
        $caseStatement .= "ELSE 'Lainnya' END as provinsi";

        // Query untuk menghitung jumlah laporan per provinsi
        return Laporan::selectRaw("$caseStatement, COUNT(*) as total")
            ->groupBy('provinsi')
            ->get();
    }

    public function showChart()
    {
        $provinsiData = $this->getProvinsiData()
            ->sortByDesc('total') // Mengurutkan berdasarkan jumlah laporan terbanyak
            ->values(); // Reset index agar terurut rapi untuk view

        return view('admin.index', compact('provinsiData'));
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
