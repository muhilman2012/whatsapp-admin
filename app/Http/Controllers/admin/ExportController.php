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
        // Validasi input tanggal
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        // Ambil tanggal yang dipilih
        $tanggal = $request->tanggal;

        // Filter data berdasarkan tanggal
        $data = Laporan::whereDate('created_at', $tanggal)->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pada tanggal tersebut.');
        }

        $formattedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $tanggal)->format('d-m-Y');
        // Export data ke file Excel
        return Excel::download(new LaporanExport($data), 'laporan_' . $formattedDate . '.xlsx');
    }
}
