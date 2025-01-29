<?php

namespace App\Http\Livewire\Admin\Laporan;

use App\Models\Laporan;
use App\Models\admins;
use App\Models\Assignment;
use Livewire\Component;
use Livewire\WithPagination;

class Terdisposisi extends Component
{
    use WithPagination;

    public $nomor_tiket;
    public $search, $pages;
    public $filterKategori = '';
    public $sortField = 'created_at'; // Default field untuk sorting
    public $sortDirection = 'asc'; // Default direction untuk sorting
    public $selectedData = [];
    public $selectedKategori = null;
    public $selectedDisposisi = null;
    public $selectAll = false; // Untuk status checkbox "Select All"
    public $selected = [];    // Array ID laporan yang dipilih
    public $selectedAnalis = null; // ID Analis yang dipilih
    public $selectedDeputi; // Properti untuk menyimpan deputi yang 
    public $analisList = []; // Daftar analis yang akan ditampilkan
    public $assignNotes; // Catatan untuk analis
    public $filterAssignment = ''; // Properti untuk menyimpan filter
    public $filterStatus = ''; // Untuk filter status
    public $tanggal; // Properti untuk menyimpan tanggal yang dipilih
    public $kategoriUnit;

    protected $listeners = ["deleteAction" => "delete"];

    protected $queryString = ['search', 'filterKategori', 'sortField', 'sortDirection'];

    public function mount()
    {
        $this->pages = 25;

        // Terima filter kategori dari URL
        $this->filterKategori = request()->get('filterKategori', '');

        // Dapatkan informasi user yang sedang login
        $user = auth('admin')->user();

        // Muat data kategori sesuai unit pengguna
        $this->kategoriUnit = Laporan::getKategoriByUnit($user->unit);
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

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()  
    {  
        $user = auth()->guard('admin')->user(); // Ambil data pengguna saat ini  
    
        $namaDeputi = [  
            'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',  
            'deputi_2' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia',  
            'deputi_3' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',  
            'deputi_4' => 'Deputi Bidang Administrasi',  
        ];  
    
        // Ambil kategori dari getter  
        $kategoriSP4NLapor = Laporan::getKategoriSP4NLapor(); // SP4N Lapor  
        $kategoriBaru = Laporan::getKategoriBaru(); // Kategori Baru  
    
        // Query data laporan  
        $data = Laporan::with(['assignments.assignedTo', 'assignments.assignedBy']);  
    
        // Filter berdasarkan role pengguna  
        if ($user->role === 'asdep') {  
            // Asdep hanya dapat melihat laporan yang di-disposisi kepada mereka  
            $data->whereHas('assignments', function ($query) use ($user) {  
                $query->where('assigned_by', $user->id_admins); // Filter berdasarkan assignedBy  
            });  
        } elseif (in_array($user->role, ['admin', 'superadmin'])) {  
            // Jika pengguna adalah admin atau superadmin, ambil semua laporan yang sudah ter-assign  
            $data->whereHas('assignments'); // Hanya ambil laporan yang memiliki assignment  
        } elseif (in_array($user->role, ['deputi_1', 'deputi_2', 'deputi_3', 'deputi_4'])) {  
            // Deputi hanya dapat melihat laporan yang di-disposisi kepada mereka  
            $data->whereHas('assignments', function ($query) use ($user) {  
                $query->whereHas('assignedBy', function ($subQuery) use ($user) {  
                    $subQuery->where('deputi', $user->deputi); // Cocokkan dengan kolom deputi  
                });  
            }); 
        } elseif ($user->role === 'analis') {  
            // Jika pengguna adalah analis, filter berdasarkan assignment  
            $data->whereHas('assignments', function ($query) use ($user) {  
                $query->where('analis_id', $user->id_admins); // Filter berdasarkan analis yang login  
            });  
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
    
        // Filter berdasarkan status  
        if (!empty($this->filterStatus)) {  
            $data->where('status', $this->filterStatus);  
        }  
    
        // Filter berdasarkan tanggal  
        if (!empty($this->tanggal)) {  
            $data->whereDate('created_at', $this->tanggal);  
        }  
    
        // Paginate data  
        $data = $data->orderBy($this->sortField, $this->sortDirection)->paginate($this->pages);  
    
        return view('livewire.admin.laporan.terdisposisi', [  
            'data' => $data,  
            'kategoriSP4NLapor' => $kategoriSP4NLapor,  
            'kategoriBaru' => $kategoriBaru,  
            'namaDeputi' => $namaDeputi,  
            'analisList' => $this->analisList,  
        ]);
    }
}