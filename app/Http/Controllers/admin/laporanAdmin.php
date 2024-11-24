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
    public function index()
    {
        return view('admin.laporan.data');
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

        // Data dummy untuk kategori, klasifikasi, dan disposisi
        $kategori = ['Bantuan Sosial', 'Hukum', 'Ekonomi dan Keuangan', 'Ketenagakerjaan',  'Pendidikan', 'Kesehatan', 'Infrastruktur', 'Lainnya'];
        $klasifikasi = ['Urgensi Tinggi', 'Urgensi Sedang', 'Urgensi Rendah'];
        $disposisi = ['D1', 'D2', 'D3', 'D4'];

        return view('admin.laporan.edit', compact('data', 'kategori', 'klasifikasi', 'disposisi'));
    }

    public function update(Request $request, $nomor_tiket)
    {
        $data = Laporan::where('nomor_tiket', $nomor_tiket)->firstOrFail();

        $data->update($request->only([
            'status', 'tanggapan', 'kategori', 'klasifikasi', 'disposisi'
        ]));

        return redirect()->route('admin.laporan.detail', $nomor_tiket)->with('success', 'Data pengaduan berhasil diperbarui.');
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
