<?php

namespace App\Http\Livewire\Admin\UserManagement;

use Livewire\Component;
use App\Models\admins;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;


class Data extends Component
{
    use WithPagination;

    public $search = ''; // Inisialisasi pencarian
    public $pages = 25; // Jumlah halaman default
    public $id_admins; // ID pengguna yang akan dihapus

    // Properti untuk modal tambah user
    public $nama, $email, $role, $jabatan, $deputi, $unit;
    protected $listeners = ["deleteAction" => "delete"];

    public function mount()
    {
        // Tidak perlu melakukan apa-apa di sini
    }

    public function removed($id_admins)
    {
        $this->id_admins = $id_admins; // Simpan ID admin yang akan dihapus
        $this->dispatchBrowserEvent('deleteConfirmed'); // Kirim event ke browser
    }

    public function delete()
    {
        $data = admins::find($this->id_admins); // Menggunakan find untuk mendapatkan data berdasarkan ID

        if ($data) {
            $data->delete(); // Hapus data
            session()->flash('success', 'Data berhasil dihapus!'); // Flash message sukses
        } else {
            session()->flash('error', 'Data tidak ditemukan!'); // Flash message error
        }
    }

    public function addUser()
    {
        try {
            $this->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:admins,email',
                'role' => 'required|in:superadmin,admin,deputi_1,deputi_2,deputi_3,deputi_4,analis',
                'jabatan' => 'nullable|string|max:255',
                'deputi' => 'nullable|string',
                'unit' => 'nullable|string',
            ],
            [
                'email.unique' => 'Email sudah digunakan. Harap gunakan email lain.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'nama.required' => 'Nama wajib diisi.',
                'role.required' => 'Role wajib dipilih.',
            ]);

            admins::create([
                'username' => $this->nama,
                'nama' => $this->nama,
                'email' => $this->email,
                'password' => Hash::make('SETwapres@2024#'), // Password default
                'avatar' => 'sample-images.png',
                'address' => 'Jl. Kebon Sirih 14, Jakarta', // Default jika kosong
                'role' => $this->role,
                'jabatan' => $this->jabatan,
                'deputi' => $this->deputi,
                'unit' => $this->unit,
            ]);

            session()->flash('success', 'User berhasil ditambahkan dengan password default: SETwapres@2024#');
            $this->reset(['nama', 'email', 'role', 'jabatan', 'deputi', 'unit']); // Reset form
            $this->dispatchBrowserEvent('closeModal'); // Tutup modal
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Query data admins
        $data = admins::query(); // Menggunakan query builder untuk memulai query

        // Pencarian berdasarkan kolom tertentu
        if (!empty($this->search)) {
            $data->where(function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%')
                      ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                      ->orWhere('deputi', 'like', '%' . $this->search . '%');
            });
        }

        // Paginate data
        $data = $data->paginate($this->pages);

        return view('livewire.admin.user-management.data', [
            'data' => $data,
        ]);
    }
}
