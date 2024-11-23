<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class indexAdmin extends Controller
{
    // show dashboard
    public function index(){
        $Laporan = Laporan::count();
        return view('admin.index', [
            'laporan'      => $Laporan,
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
