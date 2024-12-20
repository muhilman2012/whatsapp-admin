<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\ExportPdfJob;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function exportByDate(Request $request)
    {
        $adminRole = auth('admin')->user()->role;
        $kategoriDeputi = Laporan::getKategoriDeputi();

        // Tentukan kategori sesuai role
        $kategoriKataKunci = Laporan::getKategoriKataKunci();
        $kategori = $adminRole === 'admin'
            ? array_keys($kategoriKataKunci) // Semua kategori untuk admin
            : ($kategoriDeputi[$adminRole] ?? []); // Kategori sesuai role Deputi

        // Filter berdasarkan kategori dan tanggal
        $tanggal = $request->tanggal;
        $data = Laporan::whereDate('created_at', $tanggal)
            ->whereIn('kategori', $kategori)
            ->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pada tanggal tersebut.');
        }

        return Excel::download(new LaporanExport($data), 'laporan_' . $tanggal . '.xlsx');
    }

    public function exportAll()
    {
        $adminRole = auth('admin')->user()->role;
        $kategoriDeputi = Laporan::getKategoriDeputi();

        // Tentukan kategori sesuai role
        $kategoriKataKunci = Laporan::getKategoriKataKunci();
        $kategori = $adminRole === 'admin'
            ? array_keys($kategoriKataKunci) // Semua kategori untuk admin
            : ($kategoriDeputi[$adminRole] ?? []); // Kategori sesuai role Deputi

        // Ambil semua data berdasarkan kategori
        $data = Laporan::whereIn('kategori', $kategori)->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        return Excel::download(new LaporanExport($data), 'laporan_all_data.xlsx');
    }

    public function exportPdf()
    {
        // Retrieve the current admin role and categories they have access to
        $adminRole = auth('admin')->user()->role;
        $kategoriDeputi = Laporan::getKategoriDeputi();
        $kategoriKataKunci = Laporan::getKategoriKataKunci();
        $kategori = $adminRole === 'admin'
            ? array_keys($kategoriKataKunci) // All categories for admin
            : ($kategoriDeputi[$adminRole] ?? []); // Categories according to the deputy's role

        // Retrieve all data for the selected categories
        $data = Laporan::whereIn('kategori', $kategori)->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        // Generate the file name for the export
        $fileName = 'rekap_lapor_' . now()->format('d-m-Y') . '.pdf';

        // Dispatch the export job to the queue
        ExportPdfJob::dispatch($data, $fileName);

        // Redirect the user with a message indicating that the export is in progress
        return redirect()->back()->with('message', 'Proses ekspor PDF sedang berjalan. File akan diunduh otomatis saat selesai.');
    }

    public function exportByDatePdf(Request $request)
    {
        // Validasi input tanggal
        $tanggal = $request->tanggal;
        if (!$tanggal) {
            return redirect()->back()->with('error', 'Tanggal harus dipilih.');
        }

        $adminRole = auth('admin')->user()->role;
        $kategoriDeputi = Laporan::getKategoriDeputi();

        // Tentukan kategori sesuai role
        $kategoriKataKunci = Laporan::getKategoriKataKunci();
        $kategori = $adminRole === 'admin'
            ? array_keys($kategoriKataKunci) // Semua kategori untuk admin
            : ($kategoriDeputi[$adminRole] ?? []); // Kategori sesuai role Deputi

        // Filter data berdasarkan kategori dan tanggal
        $data = Laporan::whereDate('created_at', $tanggal)
            ->whereIn('kategori', $kategori)
            ->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pada tanggal tersebut.');
        }

        // Tentukan nama file
        $formattedDate = \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
        $fileName = 'laporan_tanggal_' . $formattedDate . '.pdf';

        // Dispatch job
        ExportPdfJob::dispatch($data, $fileName);

        // Redirect kembali ke halaman sebelumnya dengan pesan
        return redirect()->back()->with('message', 'Proses ekspor PDF sedang berjalan. File akan diunduh otomatis ketika selesai.');
    }

    public function checkExportStatus(Request $request)
    {
        $fileName = $request->file_name;
        $filePath = 'exports/' . $fileName;

        if (\Illuminate\Support\Facades\Storage::exists($filePath)) {
            return response()->json([
                'ready' => true,
                'download_url' => url('storage/' . $filePath),
            ]);
        }

        return response()->json(['ready' => false]);
    }

    public function exportFilteredData(Request $request)
    {
        // Ambil user yang sedang login
        $user = auth()->guard('admin')->user();

        // Query data berdasarkan filter
        $data = Laporan::query();

        // Filter berdasarkan role pengguna
        if ($user->role === 'analis') {
            $data->whereHas('assignment', function ($query) use ($user) {
                $query->where('analis_id', $user->id_admins);
            });
        } elseif ($user->role !== 'admin') {
            $data->where(function ($query) use ($user) {
                $query->where('disposisi', $user->role)
                    ->orWhere('disposisi_terbaru', $user->role);
            });
        }

        // Filter berdasarkan kategori
        if ($request->has('filterKategori') && !empty($request->filterKategori)) {
            $data->where('kategori', $request->filterKategori);
        }

        // Filter berdasarkan status
        if ($request->has('filterStatus') && !empty($request->filterStatus)) {
            $data->where('status', $request->filterStatus);
        }

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $data->where(function ($query) use ($request) {
                $query->where('nomor_tiket', 'like', '%' . $request->search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('nik', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhere('judul', 'like', '%' . $request->search . '%');
            });
        }

        $data = $data->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data yang sesuai untuk diekspor.');
        }

        return Excel::download(new LaporanExport($data), 'laporan_filtered.xlsx');
    }

    public function exportFilteredPdf(Request $request)
    {
        // Ambil user yang sedang login
        $user = auth()->guard('admin')->user();

        // Query data berdasarkan filter
        $data = Laporan::query();

        // Filter berdasarkan role pengguna
        if ($user->role === 'analis') {
            $data->whereHas('assignment', function ($query) use ($user) {
                $query->where('analis_id', $user->id_admins);
            });
        } elseif ($user->role !== 'admin') {
            $data->where(function ($query) use ($user) {
                $query->where('disposisi', $user->role)
                    ->orWhere('disposisi_terbaru', $user->role);
            });
        }

        // Filter berdasarkan kategori
        if ($request->has('filterKategori') && !empty($request->filterKategori)) {
            $data->where('kategori', $request->filterKategori);
        }

        // Filter berdasarkan status
        if ($request->has('filterStatus') && !empty($request->filterStatus)) {
            $data->where('status', $request->filterStatus);
        }

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $data->where(function ($query) use ($request) {
                $query->where('nomor_tiket', 'like', '%' . $request->search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('nik', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhere('judul', 'like', '%' . $request->search . '%');
            });
        }

        $data = $data->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data yang sesuai untuk diekspor.');
        }

        // Tentukan nama file
        $fileName = 'laporan_filtered_' . now()->format('d-m-Y') . '.pdf';

        // Dispatch job untuk membuat PDF
        ExportPdfJob::dispatch($data, $fileName);

        return redirect()->back()->with('message', 'Proses ekspor PDF sedang berjalan. File akan diunduh otomatis ketika selesai.');
    }
}
