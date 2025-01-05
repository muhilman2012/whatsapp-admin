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
        $user = auth()->guard('admin')->user(); // Ambil data pengguna yang sedang login
        $query = Laporan::whereNotNull('disposisi_terbaru');

        // Filter berdasarkan role pengguna
        if (in_array($user->role, ['superadmin', 'admin'])) {
            // Superadmin dan Admin dapat melihat semua data pelimpahan
            // Tidak perlu filter tambahan
        } elseif ($user->role === 'asdep') {
            // Ambil kategori berdasarkan unit asdep
            $kategoriByUnit = Laporan::getKategoriByUnit($user->unit);
            $query->whereIn('kategori', $kategoriByUnit); // Filter berdasarkan kategori unit
        } elseif ($user->role === 'analis') {
            // Analis hanya dapat melihat laporan yang di-assign kepada mereka
            $query->whereHas('assignment', function ($q) use ($user) {
                $q->where('analis_id', $user->id_admins); // Filter berdasarkan analis yang login
            });
        }

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
