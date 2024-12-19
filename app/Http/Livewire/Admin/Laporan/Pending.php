<?php

namespace App\Http\Livewire\Admin\Laporan;

use App\Models\Laporan;
use Livewire\Component;
use Livewire\WithPagination;

class Pending extends Component
{
    use WithPagination;

    public $search = ''; // Pencarian
    public $pages = 25;  // Jumlah data per halaman

    protected $queryString = ['search', 'pages'];

    // Mereset halaman saat pencarian berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Query untuk mendapatkan data dengan status Pending
        $query = Laporan::where('status_analisis', 'Pending');

        // Filter berdasarkan search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nomor_tiket', 'like', '%' . $this->search . '%')
                  ->orWhere('judul', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $this->search . '%');
            });
        }

        // Ambil data dengan pagination
        $data = $query->orderBy('created_at', 'desc')->paginate($this->pages);

        // Return view untuk ditampilkan
        return view('livewire.admin.laporan.pending', [
            'data' => $data,
        ]);
    }
}
