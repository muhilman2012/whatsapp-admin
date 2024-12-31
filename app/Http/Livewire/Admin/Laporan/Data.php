<?php

namespace App\Http\Livewire\Admin\Laporan;

use App\Models\Laporan;
use App\Models\admins;
use App\Models\Assignment;
use Livewire\Component;
use Livewire\WithPagination;

class Data extends Component
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

    protected $listeners = ["deleteAction" => "delete"];

    protected $queryString = ['search', 'filterKategori', 'sortField', 'sortDirection'];

    public function mount()
    {
        $this->pages = 25;

        // Terima filter kategori dari URL
        $this->filterKategori = request()->get('filterKategori', '');

        // Muat data analis berdasarkan role pengguna
        $this->loadAnalisByDeputi();
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
            'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata dan Transformasi Digital',
            'deputi_2' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia',
            'deputi_3' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
            'deputi_4' => 'Deputi Bidang Administrasi',
        ];

        // Ambil kategori dari getter
        $kategoriSP4NLapor = Laporan::getKategoriSP4NLapor(); // SP4N Lapor
        $kategoriBaru = Laporan::getKategoriBaru(); // Kategori Baru

        // Ambil daftar analis
        $analisList = admins::where('role', 'analis')->get(['id_admins', 'username']);

        // Query data laporan
        $data = Laporan::with(['assignment.assignedTo', 'assignment.assignedBy']);

        // Filter berdasarkan role pengguna
        if ($user->role === 'analis') {
            // Hanya ambil laporan yang ditugaskan ke analis yang sedang login
            $data->whereHas('assignment', function ($query) use ($user) {
                $query->where('analis_id', $user->id_admins);
            });
        } elseif (in_array($user->role, ['admin', 'superadmin'])) {
            // Jika pengguna adalah admin atau superadmin, tidak ada filter tambahan
            // Anda bisa menambahkan logika lain jika diperlukan
        } else {
            // Jika bukan admin atau superadmin, filter berdasarkan disposisi
            $data->where(function ($query) use ($user) {
                $query->where('disposisi', $user->role)
                    ->orWhere('disposisi_terbaru', $user->role);
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

        // Filter berdasarkan status assignment
        if ($this->filterAssignment === 'unassigned') {
            $data->doesntHave('assignment'); // Data belum ter-assign
        } elseif ($this->filterAssignment === 'assigned') {
            $data->has('assignment'); // Data sudah ter-assign
        }

        // Filter berdasarkan tanggal
        if (!empty($this->tanggal)) {
            $data->whereDate('created_at', $this->tanggal);
        }

        // Paginate data
        $data = $data->orderBy($this->sortField, $this->sortDirection)->paginate($this->pages);

        return view('livewire.admin.laporan.data', [
            'data' => $data,
            'kategoriSP4NLapor' => $kategoriSP4NLapor,
            'kategoriBaru' => $kategoriBaru,
            'namaDeputi' => $namaDeputi,
            'analisList' => $analisList,
        ]);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Pilih semua data yang terlihat dalam pagination
            $this->selected = Laporan::pluck('id')->toArray();
        } else {
            // Kosongkan pilihan
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        // Pastikan "Select All" berubah berdasarkan data yang dipilih
        $this->selectAll = count($this->selected) === Laporan::count();
    }

    public function updateKategoriMassal()
    {
        if (!empty($this->selected) && !empty($this->selectedKategori)) {
            Laporan::whereIn('id', $this->selected)
                ->update(['kategori' => $this->selectedKategori]);

            $this->reset(['selected', 'selectAll', 'selectedKategori']);
            session()->flash('success', 'Kategori berhasil diperbarui.');
        } else {
            session()->flash('error', 'Pilih kategori dan data terlebih dahulu.');
        }
    }

    public function updateDisposisiMassal()
    {
        if (!empty($this->selected) && !empty($this->selectedDisposisi)) {
            Laporan::whereIn('id', $this->selected)
                ->update(['disposisi' => $this->selectedDisposisi]);

            $this->reset(['selected', 'selectAll', 'selectedDisposisi']);
            session()->flash('success', 'Disposisi berhasil diperbarui.');
        } else {
            session()->flash('error', 'Pilih disposisi dan data terlebih dahulu.');
        }
    }

    public function assignToAnalis()
    {
        if (empty($this->selected) || empty($this->selectedAnalis)) {
            session()->flash('error', 'Pilih analis dan data terlebih dahulu.');
            return;
        }

        $this->validate([
            'selectedAnalis' => 'required|exists:admins,id_admins',
            'assignNotes' => 'nullable|string|max:255', // Catatan tidak wajib
        ]);

        foreach ($this->selected as $laporanId) {
            Assignment::create([
                'laporan_id' => $laporanId,
                'analis_id' => $this->selectedAnalis,
                'notes' => $this->assignNotes, // Boleh kosong
                'assigned_by' => auth('admin')->user()->id_admins,
            ]);
        }

        session()->flash('success', 'Laporan berhasil di-assign ke analis.');
        $this->reset(['selected', 'selectedAnalis', 'assignNotes', 'selectAll']);
        $this->dispatchBrowserEvent('closeModal', ['modalId' => 'assignModal']);
    }

    // Logika untuk pelimpahan data
    public function pelimpahan()
    {
        if (empty($this->selected) || empty($this->selectedDisposisi)) {
            session()->flash('error', 'Pilih data dan disposisi baru terlebih dahulu.');
            return;
        }

        Laporan::whereIn('id', $this->selected)->update([
            'disposisi_terbaru' => $this->selectedDisposisi,
            'disposisi' => null, // Hilangkan dari disposisi sebelumnya
        ]);

        $this->reset(['selected', 'selectedDisposisi']);
        session()->flash('success', 'Pelimpahan berhasil dilakukan.');
        $this->dispatchBrowserEvent('closeModal', ['modalId' => 'pelimpahanModal']);
    }

    public function getFilteredData()
    {
        $user = auth()->guard('admin')->user();

        $data = Laporan::query()
            ->with(['assignment.assignedTo', 'assignment.assignedBy']);

        // Filter berdasarkan role pengguna
        if ($user->role === 'analis') {
            $data->whereHas('assignment', function ($query) use ($user) {
                $query->where('analis_id', $user->id_admins);
            });
        } elseif ($user->role !== 'admin') {
            $data->where(function ($query) use ($user) {
                $query->where('disposisi', $user->role)
                    ->orWhere('disposisi_terbaru', $user->role);
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

        // Filter berdasarkan kategori
        if (!empty($this->filterKategori)) {
            $data->where('kategori', $this->filterKategori);
        }

        // Filter berdasarkan status
        if (!empty($this->filterStatus)) {
            $data->where('status', $this->filterStatus);
        }

        // Filter berdasarkan status assignment
        if ($this->filterAssignment === 'unassigned') {
            $data->doesntHave('assignment');
        } elseif ($this->filterAssignment === 'assigned') {
            $data->has('assignment');
        }

        return $data->orderBy($this->sortField, $this->sortDirection)->get();
    }

    public function loadAnalisByDeputi()
    {
        $user = auth()->guard('admin')->user(); // Ambil data pengguna yang sedang login

        if ($user->role === 'admin' || $user->role === 'superadmin') {
            // Admin dan Superadmin dapat melihat semua data analis, diurutkan berdasarkan username (A-Z)
            $this->analisList = admins::where('role', 'analis')
                ->orderBy('username', 'asc') // Urutkan berdasarkan abjad
                ->get(['id_admins', 'username', 'deputi']);
        } else {
            // Role deputi hanya dapat melihat analis dengan deputi yang sesuai
            $deputiName = self::$deputiMapping[$user->role] ?? null;

            if ($deputiName) {
                $this->analisList = admins::where('role', 'analis')
                    ->where('deputi', $deputiName) // Filter berdasarkan nama deputi
                    ->orderBy('username', 'asc') // Urutkan berdasarkan abjad
                    ->get(['id_admins', 'username', 'deputi']);
            } else {
                $this->analisList = collect(); // Jika tidak cocok, kosongkan data
            }
        }
    }

    public function updatedSelectedDeputi($value)
    {
        // Ambil analis berdasarkan deputi yang dipilih
        $this->analisList = $this->getAnalisByDeputi($value);
    }

    public function getAnalisByDeputi($deputi)
    {
        return admins::where('role', 'analis')
            ->where('deputi', $deputi) // Filter berdasarkan kedeputian
            ->get(['id_admins', 'username']);
    }

    private static $deputiMapping = [
        'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
        'deputi_2' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia',
        'deputi_3' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
        'deputi_4' => 'Deputi Bidang Administrasi',
    ];    
}
