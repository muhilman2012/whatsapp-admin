<?php

namespace App\Http\Livewire\Admin\Laporan;

use App\Models\Laporan;
use Livewire\Component;
use Livewire\WithPagination;

class Data extends Component
{
    use WithPagination;

    public $nomor_tiket;
    public $search, $pages;
    public $filterKategori = '';

    protected $listeners = ["deleteAction" => "delete"];

    public function mount()
    {
        $this->pages = 25;

        // Terima filter kategori dari URL
        $this->filterKategori = request()->get('filterKategori', '');
    }

    public function removed($nomor_tiket)
    {
        $this->nomor_tiket = $nomor_tiket; // Simpan nomor tiket yang akan dihapus
        $this->dispatchBrowserEvent('deleteConfirmed'); // Kirim event ke browser
    }

    public function delete()
    {
        $data = Laporan::where('nomor_tiket', $this->nomor_tiket)->first();

        if ($data) {
            $data->delete(); // Hapus data
            session()->flash('success', 'Data berhasil dihapus!'); // Flash message sukses
        } else {
            session()->flash('error', 'Data tidak ditemukan!'); // Flash message error
        }
    }

    public function render()
    {
        $user = auth()->guard('admin')->user(); // Ambil data pengguna saat ini

        // Ambil kategori dari model Laporan
        $kategori = array_keys(Laporan::getKategoriKataKunci());

        // Query data laporan
        $data = Laporan::query();

        // Filter berdasarkan role pengguna (admin atau deputi)
        if ($user->role !== 'admin') {
            $data->where('disposisi', $user->role); // Filter berdasarkan disposisi
        }

        // Pencarian berdasarkan kolom tertentu
        if (!empty($this->search)) {
            $data->where(function ($query) {
                $query->where('nomor_tiket', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%')
                    ->orWhere('judul', 'like', '%' . $this->search . '%');
            });
        }

        // Filter berdasarkan kategori yang dipilih
        if (!empty($this->filterKategori)) {
            $data->where('kategori', $this->filterKategori);
        }

        // Paginate data
        $data = $data->orderBy('created_at', 'desc')->paginate($this->pages);

        return view('livewire.admin.laporan.data', [
            'data' => $data,
            'kategori' => $kategori, // Kirim daftar kategori ke view
        ]);
    }
}
