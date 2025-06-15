<?php

namespace App\Http\Livewire\Admin\Identitas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Identitas;
use App\Models\Laporan;

class ListPengadu extends Component
{
    use WithPagination;

    public $searchTerm;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $results = Identitas::query()
            ->where('is_filled', 0)
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('nama_lengkap', 'like', '%' . $this->searchTerm . '%')
                            ->orWhere('nik', 'like', '%' . $this->searchTerm . '%')
                            ->orWhere('nomor_pengadu', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('livewire.admin.identitas.list-pengadu', [
            'results' => $results,
        ]);
    }

    public function isiLaporan($identitasId)
    {
        return redirect()->route('admin.laporan.create', ['identitas_id' => $identitasId]);
    }
}
