<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\admins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $data = admins::all();
        return view('admin.user_management.index', compact('data'));
    }

    public function create()
    {
        return view('admin.user_management.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'phone' => 'nullable|string|max:15',
            'born' => 'nullable|date',
            'address' => 'nullable|string',
            'role' => 'required|in:deputi_1,deputi_2,deputi_3,deputi_4,analis',
            'jabatan' => 'required|string|max:255',
            'deputi' => 'required|string',
            'unit' => 'required|string',
        ]);

        admins::create([
            'username' => $request->nama,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make('SETwapres@2024#'), // Password default
            'phone' => '081234567890',
            'born' => '2024-11-11',
            'avatar' => 'sample-images.png',
            'address' => 'Jl. Kebon Sirih 14, Jakarta',
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'deputi' => $request->deputi,
            'unit' => $request->unit,
        ]);

        return redirect()->route('admin.user_management.index')->with('success', 'User berhasil ditambahkan dengan password default: SETwapres@2024#');
    }

    public function edit($id_admins)
    {
        $user = admins::findOrFail($id_admins);
        return view('admin.user_management.edit', compact('user'));
    }

    public function update(Request $request, $id_admins)
    {
        $user = admins::findOrFail($id_admins);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $user->id_admins . ',id_admins',
            'role' => 'required|in:admin,deputi_1,deputi_2,deputi_3,deputi_4,analis',
            'jabatan' => 'nullable|string|max:255', // Ubah menjadi nullable
            'deputi' => 'nullable|string', // Ubah menjadi nullable
            'unit' => 'nullable|string', // Ubah menjadi nullable
            'password' => 'nullable|string|min:6|confirmed', // Validasi password
        ]);

        // Update data pengguna
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->jabatan = $request->jabatan; // Jabatan bisa kosong
        $user->deputi = $request->deputi; // Deputi bisa kosong
        $user->unit = $request->unit; // Unit bisa kosong

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save(); // Simpan perubahan

        return redirect()->route('admin.user_management.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id_admins)
    {
        $user = admins::findOrFail($id_admins);
        $user->delete();

        return redirect()->route('admin.user_management.index')->with('success', 'User berhasil dihapus.');
    }
}
