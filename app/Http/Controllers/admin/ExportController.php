<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $adminRole = auth('admin')->user()->role;
        $kategoriDeputi = Laporan::getKategoriDeputi();

        $kategoriKataKunci = Laporan::getKategoriKataKunci();
        $kategori = $adminRole === 'admin'
            ? array_keys($kategoriKataKunci)
            : ($kategoriDeputi[$adminRole] ?? []);

        $data = Laporan::whereIn('kategori', $kategori)->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        $tanggalExport = now()->format('d-m-Y');
        $jumlahPengaduan = $data->count();

        $pdf = PDF::loadView('admin.laporan.export.pdf', [
            'laporans' => $data,
            'tanggal' => $tanggalExport,
            'jumlahPengaduan' => $jumlahPengaduan,
            ])->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-left' => '5mm',
                'margin-right' => '5mm',
                'margin-top' => '5mm',
                'margin-bottom' => '5mm',
            ]);

        return $pdf->download('rekap_lapor_' . $tanggalExport . '.pdf');
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

        // Data untuk header
        $tanggalExport = \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
        $jumlahPengaduan = $data->count();

        // Generate PDF
        $pdf = PDF::loadView('admin.laporan.export.pdf', [
            'laporans' => $data,
            'tanggal' => $tanggalExport,
            'jumlahPengaduan' => $jumlahPengaduan,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan_tanggal_' . $tanggalExport . '.pdf');
    }
}
