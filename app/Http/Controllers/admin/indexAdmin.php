<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class indexAdmin extends Controller
{
    // show dashboard
    public function index()
    {
        $totalLaporan = Laporan::count(); // Total laporan
        $lakiLaki = Laporan::where('jenis_kelamin', 'L')->count(); // Pengadu laki-laki
        $perempuan = Laporan::where('jenis_kelamin', 'P')->count(); // Pengadu perempuan

        // Ambil data laporan per hari untuk chart
        $laporanHarian = Laporan::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->get();

        return view('admin.index', [
            'totalLaporan' => $totalLaporan,
            'lakiLaki' => $lakiLaki,
            'perempuan' => $perempuan,
            'laporanHarian' => $laporanHarian, // Data untuk chart
        ]);
    }

    public function logout()
    {
        if(Auth::guard('admin')->check()){
            Auth::guard('admin')->logout();
            return redirect('/');
        }
    }

}
