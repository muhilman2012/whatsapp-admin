<?php

namespace App\Http\Livewire\Admin\Laporan;

use Livewire\Component;
use App\Models\Laporan;
use Livewire\WithPagination;

class Pelimpahan extends Component
{
    use WithPagination;

    public $search = '';
    public $pages = 25; // Default pagination

    protected $queryString = ['search', 'pages'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Laporan::whereNotNull('disposisi_terbaru');

        // Pencarian
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nomor_tiket', 'like', '%' . $this->search . '%')
                  ->orWhere('judul', 'like', '%' . $this->search . '%')
                  ->orWhere('kategori', 'like', '%' . $this->search . '%');
            });
        }

        // Pagination
        $data = $query->orderBy('created_at', 'desc')->paginate($this->pages);

        return view('livewire.admin.laporan.pelimpahan', [
            'data' => $data,
        ]);
    }
}
