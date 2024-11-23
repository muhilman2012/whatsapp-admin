<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class profileAdmin extends Controller
{
    // show profile admin
    public function index()
    {
        return view('admin.profile.index');
    }
}
