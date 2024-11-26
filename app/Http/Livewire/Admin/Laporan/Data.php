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

    protected $listeners = ["deleteAction" => "delete"];

    public function removed($nomor_tiket){
        $this->nomor_tiket = $nomor_tiket;  // Gunakan variabel yang baru
        $this->dispatchBrowserEvent('deleteConfirmed');
    }

    public function delete()
    {
        // Pastikan nomor_tiket di-query sebagai nilai, bukan kolom
        $data = Laporan::where('nomor_tiket', '=', $this->nomor_tiket)->first();

        if ($data) {
            $data->delete(); // Hapus data
            session()->flash('success', 'Data telah Dihapus!'); // Flash message sukses
        } else {
            session()->flash('error', 'Maaf, data tidak ditemukan!'); // Flash message error
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
                ->orWhere('status', 'like', '%' . $this->search . '%')
                ->orWhere('judul', 'like', '%' . $this->search . '%')
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
