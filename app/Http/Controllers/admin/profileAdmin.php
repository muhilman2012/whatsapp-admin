<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginLog;

class profileAdmin extends Controller
{
    // show profile admin
    public function index()
    {
        $logs = LoginLog::where('user_id', auth('admin')->user()->id_admins)  // Ambil log login berdasarkan user_id
                    ->orderBy('created_at', 'desc')  // Urutkan berdasarkan waktu login terbaru
                    ->take(10)  // Ambil hanya 10 data terbaru
                    ->get();

        return view('admin.profile.index', compact('logs'));
    }
}
