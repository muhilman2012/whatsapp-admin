<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\admins;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class authAdmin extends Controller
{
    public function login()
    {
        if(Auth::guard('admin')->check()){
            return redirect()->route('admin.index');
        }
        return view('auth.loginAdmin');
    }

    public function loginPost(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|min:4|email|max:255',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Masukan alamat email!',
            'password.required' => 'Password tidak boleh kosong!',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        } else {
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
                // Menyimpan log login
                LoginLog::create([
                    'user_id' => auth('admin')->user()->id_admins,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ]);
                
                return redirect()->route('admin.index');
            } else {
                return redirect()->back()->with('error', 'Email dan Password Anda Salah!');
            }
        }
    }

    public function register()
    {
        if(Auth::guard('admin')->check()){
            return redirect()->route('admin.index');
        }
        return view('auth.registerAdmin');
    }

    public function registerPost(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required|max:100',
            'email' => 'required|min:8|email|unique:admins|max:255',
            'password' => 'required|confirmed|min:10',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        } else {
            try {
                $avatar = "sample-avatar.png";
                $save = admins::create([
                    'username' => $request->username,
                    'email'    => $request->email,
                    'password' => bcrypt($request->password),
                    'avatar'   => $avatar,
                ]);
                Auth::guard('admin')->login($save);
                return redirect()->route('login')->with('success', 'Yay, your registry is success');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Maaf sorry database is busy, try again later!');
            }
        }
    }

    public function logout()
    {
        if(Auth::guard('admin')->check()){
            Auth::guard('admin')->logout();
            return redirect()->route('index');
        }
    }
}
