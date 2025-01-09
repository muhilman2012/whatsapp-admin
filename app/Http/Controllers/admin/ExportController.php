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

        $data = Laporan::query();

        if ($user->role !== 'admin' && $user->role !== 'superadmin') {
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
        $kategoriDeputi = Laporan::getKategoriDeputi();
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
