<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\admins;
use App\Models\Log;
use App\Models\Assignment;
use App\Models\Notification;
use App\Models\Institution;
use App\Models\LaporanForwarding;
use App\Models\Dokumen;
use App\Models\Identitas;
use App\Models\ApiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class laporanAdmin extends Controller
{
    public function index(Request $request)
    {
        $userRole = auth()->guard('admin')->user()->role; // Ambil role admin

        // Daftar kategori yang sesuai untuk Deputi
        $kategoriDeputi = [
            'deputi_1' => ['Ekonomi dan Keuangan', 'Lingkungan Hidup dan Kehutanan', 'Pekerjaan Umum dan Penataan Ruang', 'Pertanian dan Peternakan', 'Pemulihan Ekonomi Nasional', 'Energi dan Sumber Daya Alam', 'Mudik', 'Perairan', 'Perhubungan', 'Teknologi Informasi dan Komunikasi', 'Perlindungan Konsumen', 'Pariwisata dan Ekonomi Kreatif', 'Industri dan Perdagangan', 'Perumahan'],
            'deputi_2' => ['Agama', 'Corona Virus', 'Kesehatan', 'Kesetaraan Gender dan Sosial Inklusif', 'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi', 'Pendidikan dan Kebudayaan', 'Sosial dan Kesejahteraan', 'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)', 'Penanggulangan Bencana', 'Ketenagakerjaan', 'Kependudukan', 'Pemberdayaan Masyarakat, Koperasi, dan UMKM', 'Daerah Perbatasan', 'Kepemudaan dan Olahraga', 'Keluarga Berencana'],
            'deputi_3' => ['Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat', 'Politik dan Hukum', 'Politisasi ASN', 'SP4N Lapor', 'Netralitas ASN', 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika dan Prekursor Narkotika (P4GN)', 'Manajemen ASN', 'Luar Negeri', 'Pertanahan', 'Pelayanan Publik', 'TNI/Polri'],
            'deputi_4' => ['Topik Khusus', 'Topik Lainnya', 'Bantuan Masyarakat'],
        ];

        // Ambil kategori sesuai role pengguna
        if (in_array($userRole, ['superadmin', 'admin'])) {
            // Semua kategori untuk superadmin dan admin
            $kategori = array_keys($kategoriDeputi);
        } elseif ($userRole === 'asdep') {
            // Jika pengguna adalah asdep, ambil kategori berdasarkan unit
            $kategori = Laporan::getKategoriByUnit(auth()->guard('admin')->user()->unit);
        } else {
            // Kategori sesuai role Deputi
            $kategori = $kategoriDeputi[$userRole] ?? [];
        }

        // Ambil parameter `type` untuk menentukan jenis laporan
        $type = $request->query('type', 'all'); // Default ke `all`
        $validTypes = ['all', 'pelimpahan', 'pending', 'revisi', 'approved', 'terdisposisi', 'pencarian'];

        if (!in_array($type, $validTypes)) {
            abort(404, 'Halaman tidak ditemukan.');
        }

        // Judul halaman berdasarkan `type`
        $pageTitle = match ($type) {
            'pelimpahan' => 'Laporan Pelimpahan',
            'pending' => 'Laporan Pending',
            'revisi' => 'Laporan Revisi',
            'approved' => 'Laporan Approved',
            'terdisposisi' => 'Laporan Terdisposisi',
            'all' => 'Semua Data Laporan',
            'pencarian' => 'Pencarian Data Laporan',
        };

        // Query data berdasarkan tipe laporan
        $data = Laporan::query()
            ->when($type === 'pelimpahan', function ($query) {
                $query->whereNotNull('disposisi_terbaru'); // Pelimpahan
            })
            ->when($type === 'pending', function ($query) {
                $query->where('status_analisis', 'Pending'); // Pending
            })
            ->when($type === 'rejected', function ($query) {
                $query->where('status_analisis', 'Rejected'); // Rejected
            })
            ->when($type === 'approved', function ($query) {
                $query->where('status_analisis', 'Approved'); // Approved
            })
            ->when($type === 'terdisposisi', function ($query) {  
                $query->whereHas('assignments'); // Hanya ambil laporan yang memiliki assignment  
            })
            ->when($request->filterKategori, function ($query) use ($request) {
                $query->where('kategori', $request->filterKategori);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('nomor_tiket', 'like', '%' . $request->search . '%')
                        ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%')
                        ->orWhere('nik', 'like', '%' . $request->search . '%')
                        ->orWhere('status', 'like', '%' . $request->search . '%')
                        ->orWhere('judul', 'like', '%' . $request->search . '%');
                });
            })
            ->when($type === 'all' && !in_array($userRole, ['superadmin', 'admin']), function ($query) use ($kategori) {
                $query->whereIn('kategori', $kategori); // Semua data sesuai role deputi
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.laporan.data', compact('data', 'kategori', 'type', 'pageTitle'));
    }

    public function create(Request $request)
    {
        $kategoriDeputi = Laporan::getKategoriDeputi2();
        $namaDeputi = [
            'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata dan Transformasi Digital',
            'deputi_2' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia',
            'deputi_3' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
            'deputi_4' => 'Deputi Bidang Administrasi',
        ];

        $identitas = null;
        if ($request->has('identitas_id')) {
            $identitas = \App\Models\Identitas::find($request->identitas_id);
        }

        return view('admin.laporan.create', compact('namaDeputi', 'kategoriDeputi', 'identitas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_pengadu' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat_lengkap' => 'required',
            'judul' => 'required|max:255',
            'detail' => 'required',
            'dokumen_pendukung.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'kategori' => 'required',
            'lokasi' => 'nullable',
            'tanggal_kejadian' => 'nullable|date',
            'sumber_pengaduan' => 'required'
        ]);

        $nomorTiket = $this->generateNomorTiket();

        try {
            // Ambil data identitas jika ada
            $identitas = \App\Models\Identitas::where('nik', $validated['nik'])->first();

            // Ambil URL KTP jika ada
            $pathKtp = $identitas && $identitas->foto_ktp ? $identitas->foto_ktp : null;

            // Simpan laporan
            $laporan = Laporan::create([
                'nomor_tiket' => $nomorTiket,
                'nomor_pengadu' => $validated['nomor_pengadu'],
                'email' => $validated['email'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik' => $validated['nik'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'alamat_lengkap' => $validated['alamat_lengkap'],
                'judul' => $validated['judul'],
                'detail' => $validated['detail'],
                'kategori' => $validated['kategori'],
                'lokasi' => $validated['lokasi'],
                'tanggal_kejadian' => $validated['tanggal_kejadian'],
                'sumber_pengaduan' => $validated['sumber_pengaduan'],
                'petugas' => auth('admin')->user()->nama,
                'dokumen_ktp' => $pathKtp,
            ]);

            // Simpan dokumen pendukung (jika ada)
            if ($request->hasFile('dokumen_pendukung')) {
                foreach ($request->file('dokumen_pendukung') as $index => $file) {
                    $fileName = $nomorTiket . ($index > 0 ? "_{$index}" : '') . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('public/dokumen', $fileName);

                    logger()->info('File diterima: ' . $fileName);

                    Dokumen::create([
                        'laporan_id' => $laporan->id,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                    ]);
                }
            }

            // Update status identitas jika ditemukan
            if ($identitas) {
                $identitas->is_filled = 1;
                $identitas->save();
            }

            // Log aktivitas
            Log::create([
                'laporan_id' => $laporan->id,
                'activity' => 'Laporan baru berhasil dibuat',
                'user_id' => auth('admin')->user()->id_admins,
            ]);

            return response()->json(['redirect_url' => route('admin.laporan.detail2', ['nomor_tiket' => $laporan->nomor_tiket])]);

        } catch (\Exception $e) {
            logger()->error('Error saat menciptakan laporan: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Error saat menciptakan laporan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate Nomor Tiket Unik Berupa Angka
     *
     * @return string
     */
    private function generateNomorTiket()
    {
        do {
            $nomorTiket = str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT); // Generate angka 7 digit
        } while (Laporan::where('nomor_tiket', $nomorTiket)->exists()); // Pastikan unik

        logger()->info('Nomor tiket unik dihasilkan:', ['nomor_tiket' => $nomorTiket]);
        return $nomorTiket;
    }

    public function show($nomor_tiket)
    {
        // Ambil data utama laporan berdasarkan nomor_tiket beserta relasi terkait
        $data = Laporan::with(['assignments.assignedTo', 'assignments.assignedBy'])
            ->where('nomor_tiket', $nomor_tiket)
            ->firstOrFail();

        // Ambil laporan duplikat berdasarkan NIK, nomor_pengadu, atau email
        $duplicateReports = Laporan::with(['assignments.assignedTo', 'assignments.assignedBy'])
            ->where('id', '!=', $data->id)
            ->where(function ($query) use ($data) {
                $query->where(function ($subQuery) use ($data) {
                    $subQuery->whereNotNull('nik')
                            ->where('nik', $data->nik);
                })
                ->orWhere(function ($subQuery) use ($data) {
                    $subQuery->whereNotNull('nomor_pengadu')
                            ->where('nomor_pengadu', '!=', '')
                            ->where('nomor_pengadu', $data->nomor_pengadu);
                })
                ->orWhere(function ($subQuery) use ($data) {
                    $subQuery->whereNotNull('email')
                            ->where('email', '!=', '')
                            ->where('email', $data->email);
                });
            })
            ->get();

        // Penugasan terakhir
        $latestAssignment = $data->assignments->last();

        // Dokumen terkait
        $dokumen = $data->dokumen;

        // Log aktivitas terkait laporan
        $logs = Log::where('laporan_id', $data->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Kirim ke view
        return view('admin.laporan.detail', compact('data', 'dokumen', 'latestAssignment', 'logs', 'duplicateReports'));
    }

    public function detail($nomor_tiket)
    {
        // Ambil data laporan utama beserta relasi assignments
        $data = Laporan::with(['assignments.assignedTo', 'assignments.assignedBy'])
            ->where('nomor_tiket', $nomor_tiket)
            ->firstOrFail();

        // Daftar nama Deputi
        $namaDeputi = [
            'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata dan Transformasi Digital',
            'deputi_2' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia',
            'deputi_3' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
            'deputi_4' => 'Deputi Bidang Administrasi',
        ];

        // Ambil laporan lain yang memiliki NIK sama ATAU nomor_pengadu/email sama (yang tidak kosong)
        $duplicateReports = Laporan::with(['assignments.assignedTo', 'assignments.assignedBy'])
            ->where('id', '!=', $data->id)
            ->where(function ($query) use ($data) {
                $query->where(function ($subQuery) use ($data) {
                    $subQuery->whereNotNull('nik')
                            ->where('nik', $data->nik);
                })
                ->orWhere(function ($subQuery) use ($data) {
                    $subQuery->whereNotNull('nomor_pengadu')
                            ->where('nomor_pengadu', '!=', '')
                            ->where('nomor_pengadu', $data->nomor_pengadu);
                })
                ->orWhere(function ($subQuery) use ($data) {
                    $subQuery->whereNotNull('email')
                            ->where('email', '!=', '')
                            ->where('email', $data->email);
                });
            })
            ->get();

        // Ambil penugasan terakhir
        $latestAssignment = $data->assignments->last();

        // Dokumen yang diunggah
        $dokumen = $data->dokumen;

        // Riwayat log laporan
        $logs = Log::where('laporan_id', $data->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Kirim ke view
        return view('admin.laporan.detail2', compact('data', 'namaDeputi', 'dokumen', 'latestAssignment', 'logs', 'duplicateReports'));
    }

    public function ubah(Request $request, $nomor_tiket)
    {
        $data = Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        // Validasi tambahan untuk dokumen pendukung
        $request->validate([
            'dokumen_pendukung.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        // Perbarui data pengaduan tanpa menyentuh dokumen_pendukung
        $data->update($request->except(['dokumen_pendukung']));

        // Handle file uploads jika ada
        if ($request->hasFile('dokumen_pendukung')) {
            $files = $request->file('dokumen_pendukung');

            // Konversi ke array jika bukan array
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $index => $file) {
                try {
                    $fileName = $data->nomor_tiket . ($index > 0 ? "_{$index}" : '') . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('public/dokumen', $fileName);

                    logger()->info('File diterima: ' . $fileName);

                    Dokumen::create([
                        'laporan_id' => $data->id,
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                    ]);

                    logger()->info('Dokumen berhasil disimpan: ' . $fileName);
                } catch (\Throwable $e) {
                    logger()->error('Gagal menyimpan dokumen pendukung: ' . $e->getMessage(), [
                        'nomor_tiket' => $data->nomor_tiket,
                        'index' => $index,
                        'file_name' => isset($fileName) ? $fileName : 'N/A',
                    ]);
                }
            }
        }

        // Menyimpan log aktivitas
        Log::create([
            'laporan_id' => $data->id,
            'activity' => 'Detail Laporan diperbarui',
            'user_id' => auth('admin')->user()->id_admins,
        ]);

        return redirect()->back()->with('success', 'Data pengaduan berhasil diperbarui.');
    }

    public function edit($nomor_tiket)
    {
        $data = Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        // Ambil kategori menggunakan getter
        $kategoriSP4NLapor = Laporan::getKategoriSP4NLapor();
        $kategoriBaru = Laporan::getKategoriBaru();

        $semuaDisposisi = Laporan::getKategoriDeputi();

        $namaDeputi = [
            'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata dan Transformasi Digital',
            'deputi_2' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia',
            'deputi_3' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
            'deputi_4' => 'Deputi Bidang Administrasi',
        ];

        // Mengambil data institusi dari database
        $institutions = Institution::orderBy('name')->get();

        if ($institutions->isEmpty()) {
            return back()->with('error', 'Tidak ada data institusi yang tersedia.');
        }

        return view('admin.laporan.edit', compact('data', 'kategoriSP4NLapor', 'kategoriBaru', 'semuaDisposisi', 'namaDeputi', 'institutions'));
    }

    public function update(Request $request, $nomor_tiket)
    {
        // Ambil data laporan berdasarkan nomor tiket
        $laporan = Laporan::where('nomor_tiket', $nomor_tiket)->first();

        // Jika laporan tidak ditemukan
        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan!');
        }

        // Simpan data lama untuk log
        $oldStatus = $laporan->status;
        $oldTanggapan = $laporan->tanggapan;

        // Update data laporan
        $laporan->update($request->only(['status', 'tanggapan']));

        // Cek apakah status berubah dan log perubahan status
        if ($laporan->wasChanged('status')) {
            Log::create([
                'laporan_id' => $laporan->id,
                'activity' => 'Status diperbarui menjadi "' . $laporan->status . '"',
                'user_id' => auth('admin')->user()->id_admins,
            ]);
        }

        // Cek apakah tanggapan berubah dan log perubahan tanggapan
        if ($laporan->wasChanged('tanggapan')) {
            Log::create([
                'laporan_id' => $laporan->id,
                'activity' => 'Tanggapan diperbarui menjadi "' . $laporan->tanggapan . '"',
                'user_id' => auth('admin')->user()->id_admins,
            ]);
        }

        // Mengambil ID analis yang ditugaskan pada laporan ini
        $assignments = Assignment::where('laporan_id', $laporan->id)->get();

        // Tentukan nama deputi berdasarkan disposisi atau disposisi_terbaru
        $deputiName = $laporan->disposisi_terbaru ?: $laporan->disposisi; // Pilih disposisi_terbaru jika ada, jika tidak pilih disposisi

        // Cari deputi yang bertanggung jawab terhadap laporan ini (disposisi_terbaru atau disposisi)
        $deputi = admins::where('role', $deputiName)->first();
        
        if ($deputi) {
            // Kirim notifikasi kepada deputi
            Notification::create([
                'assigner_id' => auth('admin')->user()->id_admins,  // ID pengirim
                'assignee_id' => $deputi->id_admins,  // ID penerima (deputi)
                'laporan_id' => $laporan->id,  // ID laporan
                'message' => 'Laporan telah diperbarui oleh analis.',
                'is_read' => false,  // Notifikasi belum dibaca
            ]);
        }

        // Kirimkan notifikasi ke asdep yang meng-assign analis tersebut
        foreach ($assignments as $assignment) {
            $assignedBy = $assignment->assignedBy; // Ambil asdep yang meng-assign

            if ($assignedBy && $assignedBy->role === 'asdep') { // Pastikan yang meng-assign adalah asdep
                // Kirim notifikasi kepada asdep
                Notification::create([
                    'assigner_id' => auth('admin')->user()->id_admins,  // ID pengirim
                    'assignee_id' => $assignedBy->id_admins,  // ID penerima (asdep)
                    'laporan_id' => $laporan->id,  // ID laporan
                    'message' => 'Analis telah memperbarui status/tanggapan',
                    'is_read' => false,  // Notifikasi belum dibaca
                ]);
            }
        }

        // Redirect ke halaman detail dengan pesan sukses
        return redirect()->route('admin.laporan.detail', $nomor_tiket)->with('success', 'Data pengaduan berhasil diperbarui.');
    }

    public function teruskanKeInstansi(Request $request, $nomor_tiket)
    {
        $request->validate([
            'institution' => 'required|exists:institutions,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $laporan = Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();
        $isAnonymous = $request->has('is_anonymous');

        try {
            $apiFirstResponse = $this->sendToApi($laporan, $isAnonymous);

            if ($apiFirstResponse['success']) {
                $complaintId = $apiFirstResponse['data']['complaint_id'] ?? null;
                $laporan->complaint_id = $complaintId;
                $laporan->save();

                // Kirim ke API kedua
                $apiSecondResponse = $this->sendRejectRequest($complaintId, $request->institution, $request->reason);

                if ($apiSecondResponse['success']) {
                    Log::create([
                        'laporan_id' => $laporan->id,
                        'activity' => 'Pengaduan diteruskan ke instansi tujuan',
                        'user_id' => auth('admin')->user()->id_admins,
                    ]);

                    LaporanForwarding::create([
                        'laporan_id' => $laporan->id,
                        'institution_id' => $request->institution,
                        'reason' => $request->reason,
                        'status' => 'terkirim',
                        'complaint_id' => $complaintId,
                        'is_anonymous' => $isAnonymous,
                        'sent_at' => now(),
                    ]);

                    return back()->with('success', 'Pengaduan berhasil diteruskan ke instansi tujuan.');
                } else {
                    LaporanForwarding::create([
                        'laporan_id' => $laporan->id,
                        'institution_id' => $request->institution,
                        'reason' => $request->reason,
                        'status' => 'gagal',
                        'error_message' => $apiSecondResponse['error'],
                        'is_anonymous' => $isAnonymous,
                        'scheduled_at' => now()->addHour(),
                    ]);

                    return back()->with('error', 'Gagal meneruskan ke instansi tujuan: ' . $apiSecondResponse['error']);
                }
            } else {
                return back()->with('error', 'Gagal mengirim ke LAPOR!: ' . $apiFirstResponse['error']);
            }
        } catch (\Exception $e) {
            logger()->error('Terjadi kesalahan saat teruskan ke instansi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function kirimKeLapor($nomor_tiket)
    {
        $laporan = Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        $apiResponse = $this->sendToApi($laporan);

        if ($apiResponse['success']) {
            $complaintId = $apiResponse['data']['results']['complaint']['id'] ?? null;
            $laporan->complaint_id = $complaintId;
            $laporan->save();

            Log::create([
                'laporan_id' => $laporan->id,
                'activity' => 'Pengaduan berhasil dikirim ke LAPOR!',
                'user_id' => auth('admin')->user()->id_admins,
            ]);

            return back()->with('success', 'Pengaduan berhasil dikirim ke LAPOR!');
        } else {
            return back()->with('error', 'Gagal mengirim ke LAPOR: ' . $apiResponse['error']);
        }
    }

    private function uploadDocuments($laporan)
    {
        $dokumenIds = [];
        $dokumens = Dokumen::where('laporan_id', $laporan->id)->get();

        foreach ($dokumens as $dokumen) {
            $filePath = storage_path('app/public/dokumen/' . $dokumen->file_name);

            if (file_exists($filePath)) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => $this->getApiSetting('auth'),
                        'token' => $this->getApiSetting('token'),
                    ])->attach(
                        'attachments[]',
                        file_get_contents($filePath),
                        basename($filePath)
                    )->post($this->getApiSetting('base_url') . '/complaints/complaint/file');

                    if ($response->successful()) {
                        $responseData = $response->json();
                        $dokumenId = $responseData['results']['docs'][0]['id'] ?? null;

                        if ($dokumenId) {
                            $dokumenIds[] = $dokumenId;

                            Log::create([
                                'laporan_id' => $laporan->id,
                                'activity' => 'Dokumen berhasil diunggah dengan ID: ' . $dokumenId,
                                'user_id' => auth('admin')->user()->id_admins,
                            ]);
                        }
                    } else {
                        logger()->error('Gagal mengunggah dokumen: ' . $dokumen->file_name, [
                            'error' => $response->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    logger()->error('Exception saat upload dokumen: ' . $dokumen->file_name, [
                        'message' => $e->getMessage()
                    ]);
                }
            } else {
                logger()->error('File tidak ditemukan: ' . $filePath);
            }
        }

        return $dokumenIds;
    }

    private function sendToApi($laporan, $isAnonymous = true)
    {
        $uploadedDocumentIds = $this->uploadDocuments($laporan);
        $attachments = implode(',', $uploadedDocumentIds);

        $content = $isAnonymous
            ? "Nomor Tiket pada Aplikasi LMW: {$laporan->nomor_tiket}\nDetail Laporan: {$laporan->detail}\nLokasi: {$laporan->lokasi}"
            : "Nomor Tiket pada Aplikasi LMW: {$laporan->nomor_tiket}, Nama Lengkap: {$laporan->nama_lengkap}, NIK: {$laporan->nik}, Alamat Lengkap: {$laporan->alamat_lengkap}, Detail Laporan: {$laporan->detail}, Lokasi: {$laporan->lokasi}";

        $data = [
            'title' => $laporan->judul,
            'content' => $content,
            'channel' => 13,
            'is_new_user_slider' => false,
            'user_id' => 5218120,
            'is_inbox' => true,
            'is_disposisi_slider' => true,
            'classification_id' => 6,
            'disposition_id' => 151345,
            'category_id' => 436,
            'priority_program_id' => null,
            'location_id' => 34,
            'community_id' => null,
            'date_of_incident' => $laporan->tanggal_kejadian,
            "copy_externals"=> null,
            'info_disposition' => '-',
            'info_attachments' => '[66]',
            'tags_raw' => '#lapormaswapres',
            'is_approval' => false,
            'is_anonymous' => $isAnonymous,
            'is_secret' => true,
            'is_priority' => true,
            'attachments' => "[$attachments]",
        ];

        $response = Http::withHeaders([
            'Authorization' => $this->getApiSetting('auth'),
            'token' => $this->getApiSetting('token'),
            'Content-Type' => 'application/json'
        ])->post($this->getApiSetting('base_url') . '/complaints/complaint-lmw', $data);

        if ($response->successful()) {
            Log::create([
                'laporan_id' => $laporan->id,
                'activity' => 'Pengaduan berhasil dikirim ke LAPOR!',
                'user_id' => auth('admin')->user()->id_admins,
            ]);

            return ['success' => true, 'data' => $response->json()];
        } else {
            Log::create([
                'laporan_id' => $laporan->id,
                'activity' => 'Gagal mengirim pengaduan ke LAPOR!',
                'user_id' => auth('admin')->user()->id_admins,
            ]);

            return ['success' => false, 'error' => $response->body()];
        }
    }

    private function sendRejectRequest($apiTicketNumber, $institution, $reason)
    {
        $url = $this->getApiSetting('base_url') . "/complaints/process/{$apiTicketNumber}/reject";

        $headers = [
            'Authorization' => $this->getApiSetting('auth'),
            'token' => $this->getApiSetting('token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $data = [
            'is_request' => 1,
            'reason' => $institution,
            'reason_description' => $reason,
            'not_authority' => 1
        ];

        $response = Http::withHeaders($headers)->post($url, $data);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        } else {
            return ['success' => false, 'error' => $response->body()];
        }
    }

    public function storeAnalis(Request $request, $nomor_tiket)
    {
        // Ambil data laporan berdasarkan nomor tiket
        $laporan = Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        // Validasi input
        $request->validate([
            'klasifikasi' => 'required|string', // Validasi klasifikasi
            'lembar_kerja_analis' => 'required|string', // Validasi lembar kerja analis
        ]);

        // Simpan lembar kerja analis dan set status analisis menjadi Pending
        $laporan->update([
            'klasifikasi' => $request->klasifikasi,
            'lembar_kerja_analis' => $request->lembar_kerja_analis,
            'status_analisis' => 'Menunggu Persetujuan', // Status analisis menjadi 'Menunggu Persetujuan'
        ]);

        // Log keberhasilan
        logger('Lembar Kerja Analis diperbarui oleh '.auth('admin')->user()->username, [
            'laporan_nomor_tiket' => $laporan->nomor_tiket,
            'lembar_kerja_analis' => $request->lembar_kerja_analis,
            'status_analisis' => $laporan->status_analisis,
            'klasifikasi' => $laporan->klasifikasi,
            'updated_by' => auth('admin')->user()->username
        ]);

        // Menyimpan log aktivitas
        Log::create([
            'laporan_id' => $laporan->id,
            'activity' => 'Laporan dianalisis oleh ' . auth('admin')->user()->nama,
            'user_id' => auth('admin')->user()->id_admins,
        ]);

        // Kirimkan notifikasi ke assigner (asdep) yang meng-assign analis ini
        $assignment = Assignment::where('laporan_id', $laporan->id)->first(); // Ambil data assignment pertama untuk laporan ini

        if ($assignment) {
            $assigner = $assignment->assignedBy; // Ambil assigner (asdep)

            if ($assigner && $assigner->role === 'asdep') {
                // Kirimkan notifikasi ke assigner (asdep)
                Notification::create([
                    'assigner_id' => auth('admin')->user()->id_admins, // ID pengirim notifikasi
                    'assignee_id' => $assigner->id_admins, // ID penerima (asdep)
                    'laporan_id' => $laporan->id, // ID laporan
                    'message' => 'Analis telah menganalisis Laporan ini',
                    'is_read' => false, // Notifikasi belum dibaca
                ]);
            }
        }

        // Redirect kembali ke halaman detail laporan dengan pesan sukses
        return redirect()->route('admin.laporan.detail', $nomor_tiket)->with('success', 'Lembar Kerja Analis berhasil disimpan.');
    }

    public function updateNama(Request $request, $nomor_tiket)
    {
        // Validasi input
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
        ]);

        // Cari laporan berdasarkan nomor_tiket
        $laporan = Laporan::where('nomor_tiket', $nomor_tiket)->first();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }

        // Update nama lengkap
        $laporan->update([
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        return redirect()->back()->with('success', 'Nama lengkap berhasil diperbarui.');
    }

    public function destroy($id)
    {
        //
    }

    public function editor(Request $request)
    {
        if($request->hasFile('upload')) {
            //get filename with extension
            $filenamewithextension = $request->file('upload')->getClientOriginalName();
       
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
       
            //get file extension
            $extension = $request->file('upload')->getClientOriginalExtension();
       
            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;
       
            //Upload File
            $request->file('upload')->move(public_path() . "/images/uploaded/", $filenametostore);
            // $request->file('upload')->storeAs('public/uploads', $filenametostore);
     
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = url('/images/uploaded/' . $filenametostore); 
            $msg = 'Image successfully uploaded'; 
            $re = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
              
            // Render HTML output 
            @header('Content-type: text/html; charset=utf-8'); 
            echo $re;
        }
    }

    public function export(Request $request)
    {
        // Validasi input tanggal
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Filter laporan berdasarkan periode waktu
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Format tanggal menjadi dd-mm-yyyy
        $formattedStartDate = \Carbon\Carbon::parse($startDate)->format('d-m-Y');
        $formattedEndDate = \Carbon\Carbon::parse($endDate)->format('d-m-Y');

        return Excel::download(
            new LaporanExport($startDate, $endDate),
            'laporan-' . $formattedStartDate . '-to-' . $formattedEndDate . '.xlsx'
        );
    }

    public function downloadPDF($nomor_tiket)
    {
        // Cari laporan berdasarkan nomor tiket
        $laporan = \App\Models\Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        // Data yang akan dikirim ke view PDF
        $data = [
            'laporan' => $laporan,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.laporan.pdf', $data);

        // Tambahkan watermark jika diperlukan
        $pdf->setPaper('A4', 'portrait');

        // Download file PDF
        return $pdf->download('Tanda_Terima_Pengaduan_untuk_KLD_' . $laporan->nomor_tiket . '.pdf');
    }

    public function tandaterimaPDF($nomor_tiket)
    {
        // Cari laporan berdasarkan nomor tiket
        $laporan = \App\Models\Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        // Data yang akan dikirim ke view PDF
        $data = [
            'laporan' => $laporan,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.laporan.tandaterima', $data);

        // Tambahkan watermark jika diperlukan
        $pdf->setPaper('A5', 'landscape');

        // Download file PDF
        return $pdf->download('Tanda_Terima_untuk_Pengadu_' . $laporan->nomor_tiket . '.pdf');
    }

    public function approval(Request $request, $nomorTiket)
    {
        // Ambil data laporan berdasarkan nomor tiket
        $laporan = Laporan::where('nomor_tiket', $nomorTiket)->firstOrFail();

        // Menentukan pesan log berdasarkan aksi approval
        $logMessage = '';
        if ($request->approval_action === 'approved') {
            $laporan->status_analisis = 'Disetujui'; // Status analisis berubah menjadi Disetujui
            $laporan->catatan_analisis = $request->catatan ?? null; // Catatan analisis (nullable)
            $logMessage = 'Hasil Analisis disetujui';

            // Kirimkan notifikasi ke analis bahwa hasil analisis disetujui
            $this->sendNotificationToAnalis($laporan, 'Hasil analisis Anda Disetujui');

        } elseif ($request->approval_action === 'rejected') {
            $laporan->status_analisis = 'Perbaikan'; // Status analisis berubah menjadi Perbaikan
            $laporan->catatan_analisis = $request->catatan ?? null; // Catatan analisis (nullable)
            $logMessage = 'Hasil Analisis perlu perbaikan';

            // Kirimkan notifikasi ke analis bahwa hasil analisis perlu diperbaiki
            $this->sendNotificationToAnalis($laporan, 'Hasil analisis Anda perlu Perbaikan');
        }

        // Simpan perubahan pada laporan
        $laporan->save();

        // Log perubahan status analisis
        logger()->info('Status analisis telah diperbarui oleh '.auth()->user()->name, [
            'old_status' => $laporan->getOriginal('status_analisis'),
            'new_status' => $laporan->status_analisis,
            'catatan' => $laporan->catatan_analisis,
            'updated_by' => auth()->user()->name
        ]);

        // Menyimpan log aktivitas dengan pesan yang sesuai
        Log::create([
            'laporan_id' => $laporan->id,
            'activity' => $logMessage,
            'user_id' => auth('admin')->user()->id_admins,
        ]);

        // Redirect ke halaman detail laporan dengan pesan sukses
        return redirect()->route('admin.laporan.detail', $nomorTiket)->with('success', 'Status analisis berhasil diperbarui!');
    }

    private function sendNotificationToAnalis($laporan, $message)
    {
        // Kirim notifikasi ke analis yang terkait
        $assignments = Assignment::where('laporan_id', $laporan->id)->get();
        foreach ($assignments as $assignment) {
            $analis = $assignment->assignedTo;

            // Kirim notifikasi
            Notification::create([
                'assigner_id' => auth('admin')->user()->id_admins,
                'assignee_id' => $analis->id_admins,
                'laporan_id' => $laporan->id,
                'message' => $message,
                'is_read' => false,
            ]);
        }
    }

    // Fungsi untuk mendapatkan nama kedeputian berdasarkan disposisi
    private function getNamaKedeputian($disposisi)
    {
        // Mapping disposisi ke nama kedeputian
        return self::$deputiMapping[$disposisi] ?? null;  
    }

    private static $deputiMapping = [
        'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
        'deputi_2' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia',
        'deputi_3' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
        'deputi_4' => 'Deputi Bidang Administrasi',
    ];

    public function laphar()
    {
        // Menampilkan view untuk membuat laporan harian
        return view('admin.laporan.laphar');
    }

    public function exportSingle(Request $request)
    {
        // Menghandle single date export
        $date = $request->date;
        $source = $request->source;
        return $this->exportReports($date, $date, $source);
    }

    public function exportRange(Request $request)
    {
        // Menghandle range date export
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $source = $request->source;
        return $this->exportReports($fromDate, $toDate, $source);
    }

    private function exportReports($startDate, $endDate, $source)
    {
        $formattedStartDate = Carbon::parse($startDate)->format('d-m-Y');
        $formattedEndDate = Carbon::parse($endDate)->format('d-m-Y');

        $query = Laporan::whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);

        // Menambahkan filter berdasarkan sumber pengaduan
        if ($source !== 'all') {
            $query->where('sumber_pengaduan', $source);
        }

        $reports = $query->get();

        // Memformat data untuk disertakan dalam PDF
        $data = [
            'reports' => $reports,
            'startDate' => $formattedStartDate,
            'endDate' => $formattedEndDate
        ];

        // Membuat PDF
        $pdf = PDF::loadView('admin.laporan.export.laphar', $data)
                ->setPaper('a4', 'landscape'); // Set orientasi landscape

        // Menyimpan PDF dengan nama yang mencakup tanggal dan sumber pengaduan
        $fileName = 'laporan-' . ($formattedStartDate == $formattedEndDate ? $formattedStartDate : $formattedStartDate . '-to-' . $formattedEndDate) . '-' . $source . '.pdf';
        return $pdf->download($fileName);
    }

    public function list()
    {
        return view('admin.laporan.list');
    }

    public function laporanforwarding()
    {
        return view('admin.laporan.forwarding');
    }

    public function getApiSetting($key)
    {
        return ApiSetting::where('key', $key)->value('value') ?? '';
    }
}