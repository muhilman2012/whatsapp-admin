<?php

namespace App\Http\Livewire\Admin\Laporan;

use App\Models\Laporan;
use App\Models\admins;
use App\Models\Assignment;
use App\Models\Notification;
use App\Models\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Pencarian extends Component
{
    public $searchTerm = '';

    public function render()
    {
        $results = collect(); // Koleksi kosong sebagai default

        if (!empty($this->searchTerm)) {
            $searchTerm = '%' . $this->searchTerm . '%';
            $results = Laporan::where('judul', 'like', $searchTerm)
                               ->orWhere('detail', 'like', $searchTerm)
                               ->orWhere('nama_lengkap', 'like', $searchTerm)
                               ->orWhere('nik', 'like', $searchTerm)
                               ->get();
        }

        return view('livewire.admin.laporan.pencarian', compact('results'));
    }
}
