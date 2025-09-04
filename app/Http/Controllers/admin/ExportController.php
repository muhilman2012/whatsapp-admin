<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\admins;
use App\Models\Assignment;
use App\Models\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\ExportPdfJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    public function exportByDate(Request $request)
    {
        $adminRole = auth('admin')->user()->role;
        $unit = auth('admin')->user()->unit;

        $tanggal = $request->tanggal;
        if (!$tanggal) {
            return redirect()->back()->with('error', 'Tanggal harus dipilih.');
        }

        $kategori = $this->getKategoriByRole($adminRole, $unit);
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
        $unit = auth('admin')->user()->unit;

        $kategori = $this->getKategoriByRole($adminRole, $unit);

        $data = $adminRole === 'admin' || $adminRole === 'superadmin'
            ? Laporan::all()
            : Laporan::whereIn('kategori', $kategori)->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        return Excel::download(new LaporanExport($data), 'laporan_all_data.xlsx');
    }

    public function exportFilteredData(Request $request)
    {
        $user = auth()->guard('admin')->user();
        $kategori = $this->getKategoriByRole($user->role, $user->unit);

        $query = Laporan::query();

        if ($user->role === 'analis') {
            $query->whereHas('assignments', function ($q) use ($user) {
                $q->where('analis_id', $user->id_admins);
            });
        } elseif (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->whereIn('kategori', $kategori);
        }

        if ($request->filled('filterKategori')) {
            $query->where('kategori', $request->filterKategori);
        }

        if ($request->filled('filterStatus')) {
            $query->where('status', $request->filterStatus);
        }

        // Logika untuk filter rentang tanggal
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_tiket', 'like', '%' . $request->search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('nik', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhere('judul', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        if ($request->filled('sumber_pengaduan')) {
            $query->where('sumber_pengaduan', $request->sumber_pengaduan);
        }

        if (!$query->exists()) {
            return redirect()->back()->with('error', 'Tidak ada data yang sesuai untuk diekspor.');
        }

        // Buat nama file
        $fileName = 'laporan_' . now()->format('Ymd');
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fileName .= '_from_' . \Carbon\Carbon::parse($request->from_date)->format('d_m_Y') . '_to_' . \Carbon\Carbon::parse($request->to_date)->format('d_m_Y');
        } elseif ($request->filled('tanggal')) {
            $fileName .= '_tanggal_' . \Carbon\Carbon::parse($request->tanggal)->format('d_m_Y');
        }

        if ($request->filled('filterKategori')) {
            $fileName .= '_kategori_' . Str::slug($request->filterKategori, '_');
        }
        if ($request->filled('filterStatus')) {
            $fileName .= '_status_' . Str::slug($request->filterStatus, '_');
        }
        if ($request->filled('sumber_pengaduan')) {
            $fileName .= '_sumber_' . Str::slug($request->sumber_pengaduan, '_');
        }

        $fileName .= '.xlsx';
        $filePath = 'public/exports/' . $fileName;

        // Simpan file Excel ke storage
        Excel::store(new LaporanExport($query), $filePath);

        $downloadLink = Storage::url('exports/' . $fileName);

        // Kirim flash message dengan link
        return redirect()->back()->with([
            'success' => 'Export berhasil.',
            'download_url' => Storage::url('exports/' . $fileName)
        ]);
    }

    public function exportPelimpahan(Request $request)
    {
        $user = auth()->guard('admin')->user();
        $kategori = $this->getKategoriByRole($user->role, $user->unit);

        $data = Laporan::query();

        // Mengelompokkan semua filter utama dalam satu kondisi OR
        $data->where(function ($query) use ($request) {
            
            // Kondisi 1: Filter untuk data yang sudah terdisposisi (data lama)
            $query->whereNotNull('disposisi')
                ->whereNotNull('disposisi_terbaru');

            // Kondisi 2: Filter untuk data yang sudah di-assign (data baru)
            if ($request->has('filterAssignment') && $request->filterAssignment === 'assigned') {
                $query->orWhere(function ($subquery) {
                    $subquery->has('assignments');
                });
            }
        });

        // Menangani filter unassigned secara terpisah
        if ($request->has('filterAssignment') && $request->filterAssignment === 'unassigned') {
            $data->doesntHave('assignments');
        }

        if ($user->role === 'analis') {
            $data->whereHas('assignments', function ($query) use ($user) {
                $query->where('analis_id', $user->id_admins);
            });
        } else if ($user->role !== 'admin' && $user->role !== 'superadmin') {
            $data->whereIn('kategori', $kategori);
        }
        
        // ... (Filter lainnya di bawah ini) ...
        if ($request->has('filterKategori') && !empty($request->filterKategori)) {
            $data->where('kategori', $request->filterKategori);
        }

        if ($request->has('filterStatus') && !empty($request->filterStatus)) {
            $data->where('status', $request->filterStatus);
        }

        if ($request->has('search') && !empty($request->search)) {
            $data->where(function ($query) use ($request) {
                $query->where('nomor_tiket', 'like', '%' . $request->search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('nik', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhere('judul', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $data->whereDate('created_at', $request->tanggal);
        }

        if ($request->has('sumber_pengaduan') && !empty($request->sumber_pengaduan)) {
            $data->where('sumber_pengaduan', $request->sumber_pengaduan);
        }

        $data = $data->get();

        // ... (Logika ekspor tetap sama) ...
        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data yang sesuai untuk diekspor.');
        }

        $fileName = 'laporan_all';
        if ($request->has('filterKategori') && !empty($request->filterKategori)) {
            $fileName .= '_by_kategori_' . str_replace(['/', '\\'], '_', str_replace(' ', '_', $request->filterKategori));
        }
        if ($request->has('filterStatus') && !empty($request->filterStatus)) {
            $fileName .= '_by_status_' . str_replace(['/', '\\'], '_', str_replace(' ', '_', $request->filterStatus));
        }
        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $fileName .= '_by_tanggal_' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
        }
        if ($request->has('sumber_pengaduan') && !empty($request->sumber_pengaduan)) {
            $fileName .= '_by_' . str_replace(['/', '\\'], '_', str_replace(' ', '_', $request->sumber_pengaduan));
        }
        if ($request->has('filterAssignment') && !empty($request->filterAssignment)) {
            $fileName .= '_by_assignment_' . $request->filterAssignment;
        }
        $fileName .= '.xlsx';

        return Excel::download(new LaporanExport($data), $fileName);
    }

    public function exportFilteredPdf(Request $request)
    {
        $adminRole = auth('admin')->user()->role;
        $unit = auth('admin')->user()->unit;

        $kategori = $this->getKategoriByRole($adminRole, $unit);

        $data = Laporan::query();

        if ($adminRole !== 'admin' && $adminRole !== 'superadmin') {
            $data->whereIn('kategori', $kategori);
        }

        if ($request->has('filterKategori') && !empty($request->filterKategori)) {
            $data->where('kategori', $request->filterKategori);
        }

        if ($request->has('filterStatus') && !empty($request->filterStatus)) {
            $data->where('status', $request->filterStatus);
        }

        if ($request->has('search') && !empty($request->search)) {
            $data->where(function ($query) use ($request) {
                $query->where('nomor_tiket', 'like', '%' . $request->search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('nik', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhere('judul', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $data->whereDate('created_at', $request->tanggal);
        }

        $data = $data->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data yang sesuai untuk diekspor.');
        }

        $fileName = 'laporan_all';
        if ($request->has('filterKategori') && !empty($request->filterKategori)) {
            $fileName .= '_by_kategori_' . str_replace(['/', '\\'], '_', str_replace(' ', '_', $request->filterKategori));
        }
        if ($request->has('filterStatus') && !empty($request->filterStatus)) {
            $fileName .= '_by_status_' . str_replace(['/', '\\'], '_', str_replace(' ', '_', $request->filterStatus));
        }
        if ($request->has('tanggal') && !empty($request->tanggal)) {
            $fileName .= '_by_tanggal_' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y');
        }
        $fileName .= '.pdf';

        // Create the PDF from the data
        $pdf = Pdf::loadView('admin.laporan.export.pdf', [
            'laporans' => $data,
            'tanggal' => now()->format('d-m-Y'),
            'jumlahPengaduan' => $data->count(),
        ])->setPaper('a4', 'landscape');
        
        return $pdf->download($fileName);
    }

    private function getKategoriByRole($role, $unit = null)
    {
        $kategoriDeputi = Laporan::getKategoriDeputi2();
        $kategoriKataKunci = Laporan::getKategoriKataKunci();

        if ($role === 'admin' || $role === 'superadmin') {
            return Laporan::distinct()->pluck('kategori')->toArray();
        } elseif ($role === 'asdep') {
            return Laporan::getKategoriByUnit($unit);
        } else {
            return $kategoriDeputi[$role] ?? [];
        }
    }  
    
    public function checkExportStatus(Request $request)  
    {  
        $fileName = $request->file_name;  
        $filePath = 'public/exports/' . $fileName;  
    
        if (\Illuminate\Support\Facades\Storage::exists($filePath)) {  
            return response()->json([  
                'ready' => true,  
                'download_url' => url('storage/exports/' . $fileName),  
            ]);  
        }  
    
        return response()->json(['ready' => false]);  
    }
}
