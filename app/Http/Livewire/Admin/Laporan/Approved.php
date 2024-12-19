<?php

namespace App\Http\Livewire\Admin\Laporan;

use App\Models\Laporan;
use Livewire\Component;
use Livewire\WithPagination;

class Approved extends Component
{
    use WithPagination;

    public $search = '';
    public $pages = 25;

    protected $queryString = ['search', 'pages'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Laporan::where('status_analisis', 'Approved');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nomor_tiket', 'like', '%' . $this->search . '%')
                  ->orWhere('judul', 'like', '%' . $this->search . '%');
            });
        }

        $data = $query->orderBy('created_at', 'desc')->paginate($this->pages);

        return view('livewire.admin.laporan.approved', [
            'data' => $data,
        ]);
    }
}
