<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;

class laporanAdmin extends Controller
{
    public function index(Request $request)
    {
        $userRole = auth()->guard('admin')->user()->role; // Ambil role admin

        // Daftar kategori yang sesuai untuk Deputi
        $kategoriDeputi = [
            'deputi_1' => ['Ekonomi dan Keuangan', 'Lingkungan Hidup dan Kehutanan', 'Pekerjaan Umum dan Penataan Ruang', 'Pertanian dan Peternakan', 'Pemulihan Ekonomi Nasional', 'Energi dan Sumber Daya Alam', 'Mudik', 'Perairan', 'Perhubungan', 'Teknologi Informasi dan Komunikasi', 'Perlindungan Konsumen', 'Pariwisata dan Ekonomi Kreatif', 'Industri dan Perdagangan', 'Perumahan'],
            'deputi_2' => ['Agama', 'Corona Virus', 'Kesehatan', 'Kesetaraan Gender dan Sosial Inklusif', 'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi', 'Pendidikan dan Kebudayaan', 'Sosial dan Kesejahteraan', 'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)', 'Penanggulangan Bencana', 'Ketenagakerjaan', 'Kependudukan', 'Pemberdayaan Masyarakat, Koperasi, dan UMKM', 'Daerah Perbatasan', 'Kepemudaan dan Olahraga', 'Keluarga Berencana'],
            'deputi_3' => ['Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat','Politik dan Hukum', 'Politisasi ASN', 'SP4N Lapor', 'Netralitas ASN', 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika dan Prekursor Narkotika (P4GN)', 'Manajemen ASN', 'Luar Negeri', 'Pertanahan'],
            'deputi_4' => ['Topik Khusus', 'Topik Lainnya', 'Bantuan Masyarakat'],
        ];

        // Ambil kategori sesuai role
        $kategori = $kategoriDeputi[$userRole] ?? [];

        // Query data
        $data = Laporan::query()
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
            ->whereIn('kategori', $kategori) // Filter kategori berdasarkan Deputi
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.laporan.data', compact('data', 'kategori'));
    }

    public function create()
    {
        return view('admin.laporan.create');
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'nomor_pengadu' => 'required|string|max:15', // Nomor pengadu wajib
            'email' => 'nullable|email|max:255', // Email opsional
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat_lengkap' => 'required',
            'judul' => 'required|max:255',
            'lokasi' => 'required|string|max:255',
            'detail' => 'required',
            'tanggal_kejadian' => 'nullable|date',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Generate nomor tiket unik berupa angka 7 digit
        $nomorTiket = $this->generateNomorTiket();

        // Proses dokumen pendukung
        $fileName = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $file = $request->file('dokumen_pendukung');
            $fileName = $nomorTiket . '.' . $file->getClientOriginalExtension(); // Rename file dengan ID tiket
            $file->move(storage_path('app/public/dokumen'), $fileName); // Simpan file di storage/public
        }

        // Simpan data ke database
        Laporan::create([
            'nomor_tiket' => $nomorTiket,
            'nomor_pengadu' => $request->nomor_pengadu,
            'email' => $request->email,
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat_lengkap' => $request->alamat_lengkap,
            'judul' => $request->judul,
            'lokasi' => $request->lokasi,
            'detail' => $request->detail,
            'lokasi' => $request->lokasi,
            'dokumen_pendukung' => $fileName,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'sumber_pengaduan' => 'tatap muka',
        ]);

        return redirect()->route('admin.laporan')->with('success', 'Laporan berhasil ditambahkan.');
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

        return $nomorTiket;
    }

    public function show($nomor_tiket)
    {
        $data = Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        return view('admin.laporan.detail', compact('data'));
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

        return view('admin.laporan.edit', compact('data', 'kategoriSP4NLapor', 'kategoriBaru', 'semuaDisposisi', 'namaDeputi'));
    }

    public function update(Request $request, $nomor_tiket)
    {
        // dd($request->all()); // Periksa data input
        $data = Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        // Validasi input
        $request->validate([
            'kategori' => 'required|string',
            'disposisi' => 'required|string',
            'status' => 'nullable|string|max:255',
            'tanggapan' => 'nullable|string',
        ]);

        // Update data
        $data->update([
            'kategori' => $request->kategori,
            'disposisi' => $request->disposisi,
            'status' => $request->status,
            'tanggapan' => $request->tanggapan,
        ]);

        logger('Data berhasil diperbarui:', $data->toArray());
        return redirect()->route('admin.laporan.detail', $nomor_tiket)->with('success', 'Data pengaduan berhasil diperbarui.');
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
        return $pdf->download('Bukti_Pengaduan_' . $laporan->nomor_tiket . '.pdf');
    }
}
