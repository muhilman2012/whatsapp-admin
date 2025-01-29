<?php

namespace App\Http\Livewire\Admin\Laporan;

use App\Models\Laporan;
use App\Models\admins;
use App\Models\Assignment;
use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Pelimpahan extends Component
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
    public $currentPageData = [];
    public $filterStatusAnalisis = ''; // Untuk filter status analisis

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

        // Muat data analis (jika diperlukan)
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
        // Ambil data pengguna yang sedang login
        $user = auth()->guard('admin')->user();

        $namaDeputi = [
            'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital',
            'deputi_2' => 'Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan dan Pembangunan Sumber Daya Manusia',
            'deputi_3' => 'Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan',
            'deputi_4' => 'Deputi Bidang Administrasi',
        ];

        // Dapatkan data terfilter menggunakan metode getFilteredData
        $data = $this->getFilteredData();

        // Simpan ID data pada halaman yang dipaginate saat ini
        $this->currentPageData = $data->pluck('id')->toArray();

        // Hitung total data yang terfilter berdasarkan kategori, status, dan pencarian
        $totalFiltered = Laporan::query()
            ->when($this->filterKategori, function ($query) {
                $query->where('kategori', $this->filterKategori);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('nomor_tiket', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%')
                        ->orWhere('judul', 'like', '%' . $this->search . '%');
                });
            })
            ->count();

        // Tentukan apakah semua data pada halaman saat ini sudah terpilih
        $this->selectAll = count(array_intersect($this->currentPageData, $this->selected)) === count($this->currentPageData);

        return view('livewire.admin.laporan.pelimpahan', [
            'data' => $data,
            'totalFiltered' => $totalFiltered,
            'kategoriSP4NLapor' => Laporan::getKategoriSP4NLapor(),
            'kategoriBaru' => Laporan::getKategoriBaru(),
            'namaDeputi' => $namaDeputi,
            'analisList' => $this->analisList,
        ]);
    }

    public function getFilteredData()
    {
        $user = auth()->guard('admin')->user(); // Ambil data pengguna saat ini

        $data = Laporan::with(['assignments.assignedTo', 'assignments.assignedBy']);

        // Filter berdasarkan role pengguna
        if ($user->role === 'asdep') {
            // Ambil kategori berdasarkan unit asdep
            $kategoriByUnit = Laporan::getKategoriByUnit($user->unit);

            if (!empty($kategoriByUnit)) {
                $data->whereIn('kategori', $kategoriByUnit);
            }
        } elseif (in_array($user->role, ['admin', 'superadmin'])) {
            // Jika pengguna adalah admin atau superadmin, tidak ada filter tambahan
        } elseif ($user->role === 'analis') {
            // Jika pengguna adalah analis, filter berdasarkan assignment
            $data->whereHas('assignments', function ($query) use ($user) {
                $query->where('analis_id', $user->id_admins);
            });
        } else {
            // Jika bukan admin, superadmin, atau analis, filter berdasarkan disposisi
            $data->where(function ($query) use ($user) {
                $query->where('disposisi', $user->role)
                    ->orWhere('disposisi_terbaru', $user->role);
            });
        }

        // Filter status analisis
        if ($this->filterStatusAnalisis) {
            $data->where('status_analisis', $this->filterStatusAnalisis);
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
            $data->doesntHave('assignments');
        } elseif ($this->filterAssignment === 'assigned') {
            $data->has('assignments');
        } elseif ($this->filterAssignment === 'unassigned_disposition') {
            $data->where(function ($query) {
                $query->whereNull('disposisi')
                    ->whereNull('disposisi_terbaru');
            });
        }

        // Filter berdasarkan tanggal
        if (!empty($this->tanggal)) {
            $data->whereDate('created_at', $this->tanggal);
        }

        // Filter berdasarkan disposisi dan disposisi_terbaru  
        $data->whereNotNull('disposisi')  
             ->whereNotNull('disposisi_terbaru'); 

        // Urutkan data berdasarkan status
        $data->orderByRaw("
            CASE
                WHEN status = 'Proses verifikasi dan telaah' THEN 1
                WHEN status = 'Menunggu kelengkapan data dukung dari Pelapor' THEN 2
                WHEN status = 'Diteruskan kepada instansi yang berwenang untuk penanganan lebih lanjut' THEN 3
                WHEN status = 'Penanganan Selesai' THEN 4
                ELSE 5
            END
        ")->orderBy($this->sortField, $this->sortDirection);

        // Paginate data berdasarkan jumlah halaman
        return $data->paginate($this->pages);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Tambahkan semua ID data pada halaman saat ini ke selected
            $this->selected = array_unique(array_merge($this->selected, $this->currentPageData));
        } else {
            // Hapus ID data pada halaman saat ini dari selected
            $this->selected = array_diff($this->selected, $this->currentPageData);
        }
    }

    public function updatedSelected()
    {
        // Perbarui status select all berdasarkan data pada halaman saat ini
        $this->selectAll = count(array_intersect($this->currentPageData, $this->selected)) === count($this->currentPageData);
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
            'assignNotes' => 'nullable|string|max:255', // Catatan opsional
        ]);

        foreach ($this->selected as $laporanId) {
            $existingAssignment = Assignment::where('laporan_id', $laporanId)->first();

            if ($existingAssignment) {
                session()->flash('error', "Laporan #{$laporanId} sudah ditugaskan ke analis lain.");
                continue;
            }

            // Buat assignment baru
            Assignment::create([
                'laporan_id' => $laporanId,
                'analis_id' => $this->selectedAnalis,
                'notes' => $this->assignNotes, // Catatan opsional
                'assigned_by' => auth('admin')->user()->id_admins,
            ]);

            // Kirim notifikasi ke analis
            Notification::create([
                'assigner_id' => auth('admin')->user()->id_admins,
                'assignee_id' => $this->selectedAnalis,
                'laporan_id' => $laporanId,
            ]);
        }

        // Reset pilihan dan tampilkan pesan sukses
        $this->reset(['selected', 'selectAll', 'selectedAnalis']);
        session()->flash('success', 'Laporan berhasil di-assign ke analis.');
    }

    public function pelimpahan(Request $request)
    {
        // Cek jika selected atau selectedDisposisi kosong
        if (empty($this->selected) || empty($this->selectedDisposisi)) {
            session()->flash('error', 'Pilih data dan disposisi baru terlebih dahulu.');
            return;
        }

        foreach ($this->selected as $laporanId) {
            $laporan = Laporan::find($laporanId);

            if (!$laporan) {
                session()->flash('error', "Laporan #{$laporanId} tidak ditemukan.");
                continue;
            }

            // Update disposisi_terbaru
            $laporan->update([
                'disposisi_terbaru' => $this->selectedDisposisi,
            ]);

            // Ambil nama kedeputian berdasarkan disposisi yang dipilih
            $deputiName = $this->getNamaKedeputian($this->selectedDisposisi); // Nama kedeputian

            // Cari assignee_id untuk deputi yang relevan
            $deputi = admins::where('role', $this->selectedDisposisi)->first();
            $assigneeId = $deputi ? $deputi->id_admins : null;  // Ambil ID deputi yang relevan

            // Jika assignee_id untuk deputi ditemukan, kirimkan notifikasi
            if ($assigneeId) {
                Notification::create([
                    'assigner_id' => auth('admin')->user()->id_admins,  // ID pengirim notifikasi
                    'assignee_id' => $assigneeId,                        // ID penerima notifikasi (deputi)
                    'laporan_id' => $laporanId,                           // ID laporan yang dipilih
                    'message' => 'Pelimpahan data ke ' . $deputiName,     // Pesan notifikasi
                    'role' => $deputiName,                                // Isi role dengan nama kedeputian
                    'is_read' => false,                                   // Status notifikasi belum dibaca
                ]);
            }

            // Kirim notifikasi ke asdep yang relevan
            $asdepUsers = admins::where('role', 'asdep')
                                ->where('deputi', $deputiName) // Filter berdasarkan nama deputi yang sesuai
                                ->get();

            foreach ($asdepUsers as $asdep) {
                Notification::create([
                    'assigner_id' => auth('admin')->user()->id_admins,  // ID pengirim notifikasi
                    'assignee_id' => $asdep->id_admins,                  // ID penerima notifikasi (asdep)
                    'laporan_id' => $laporanId,                           // ID laporan yang dipilih
                    'message' => 'Pelimpahan data ke ' . $deputiName,     // Pesan notifikasi
                    'role' => $deputiName,                                // Isi role dengan nama kedeputian
                    'is_read' => false,                                   // Status notifikasi belum dibaca
                ]);
            }
        }

        // Reset pilihan dan tampilkan pesan sukses
        $this->reset(['selected', 'selectedDisposisi']);
        session()->flash('success', 'Pelimpahan berhasil dilakukan.');
        $this->dispatchBrowserEvent('closeModal', ['modalId' => 'pelimpahanModal']);
    }

    // Fungsi untuk mendapatkan nama kedeputian berdasarkan disposisi
    private function getNamaKedeputian($disposisi)
    {
        // Mapping disposisi ke nama kedeputian
        return self::$deputiMapping[$disposisi] ?? null;  
    }

    public function loadAnalisByDeputi()    
    {    
        $user = auth()->guard('admin')->user(); // Ambil data pengguna yang sedang login    
    
        if ($user->role === 'admin' || $user->role === 'superadmin') {    
            // Admin dan Superadmin dapat melihat semua data analis, diurutkan berdasarkan username (A-Z)    
            $this->analisList = admins::where('role', 'analis')    
                ->orderBy('username', 'asc') // Urutkan berdasarkan abjad    
                ->get(['id_admins', 'username', 'deputi']);    
        } elseif ($user->role === 'asdep') {    
            // Role asdep hanya dapat melihat analis berdasarkan kolom deputi yang sama  
            $deputiName = $user->deputi; // Ambil nama deputi dari pengguna yang sedang login  
    
            $this->analisList = admins::where('role', 'analis')    
                ->where('deputi', $deputiName) // Filter berdasarkan nama deputi yang sama    
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