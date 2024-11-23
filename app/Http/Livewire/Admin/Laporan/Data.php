<?php

namespace App\Http\Livewire\Admin\Laporan;

use App\Models\Laporan;
use Livewire\Component;
use Livewire\WithPagination;

class Data extends Component
{
    use WithPagination;
    public $laporanId;
    public $search, $pages;

    protected $listeners = ["deleteAction" => "delete"];

    public function removed($laporanId)
    {
        // Logika penghapusan laporan
        Laporan::findOrFail($laporanId)->delete();

        session()->flash('message', 'Laporan berhasil dihapus!');
    }

    public function delete()
    {
        $data = Laporan::find($this->id);
        if ($data) {
            $data->delete();
            return session()->flash('success', 'Data telah Dihapus!');
        } else {
            return session()->flash('error', 'Maaf ada kesalahan!');
        }
    }

    public function mount()
    {
        $this->pages = 25;
    }
    
    public function render()
    {
        // Query pencarian
        if ($this->search) {
            $data = Laporan::where('nomor_tiket', 'like', '%' . $this->search . '%')
                ->orWhere('nama_lengkap', 'like', '%' . $this->search . '%')
                ->orWhere('nik', 'like', '%' . $this->search . '%')
                ->orderBy('created_at', 'desc')
                ->paginate($this->pages);
        } else {
            $data = Laporan::orderBy('created_at', 'desc')->paginate($this->pages);
        }

        return view('livewire.admin.laporan.data', [
            'data' => $data
        ]);
    }
}
