<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

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

        return Excel::download(new LaporanExport($data), 'laporan_' . now()->format('d-m-Y') . '.xlsx');
    }
}
