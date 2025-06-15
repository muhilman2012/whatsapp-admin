<?php

namespace App\Http\Livewire\Admin\Laporan;

use Livewire\Component;
use App\Models\LaporanForwarding;
use App\Models\Institution;
use App\Models\Laporan;
use Livewire\WithPagination;


class ForwardingList extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = LaporanForwarding::with(['laporan', 'institution'])
            ->whereHas('laporan', function ($query) {
                $query->where('nomor_tiket', 'like', "%{$this->search}%")
                      ->orWhere('nama_lengkap', 'like', "%{$this->search}%");
            })
            ->orderBy('forwarded_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.laporan.forwarding-list', [
            'data' => $data
        ]);
    }
}
