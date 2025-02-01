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
    public $kategoriUnit;
    public $currentPageData = [];
    public $filterPelimpahan = false; // Untuk filter pelimpahan
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

        $kategoriDeputi = Laporan::getKategoriDeputi2();

        $namaDeputi = [
            'deputi_1' => 'Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata dan Transformasi Digital',
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

        return view('livewire.admin.laporan.data', [
            'data' => $data,
            'totalFiltered' => $totalFiltered,
            'kategoriSP4NLapor' => Laporan::getKategoriSP4NLapor(),
            'kategoriBaru' => Laporan::getKategoriBaru(),
            'kategoriDeputi' => $kategoriDeputi,
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
            $kategoriByUnit = Laporan::getKategoriByUnit($user->unit);  
            if (!empty($kategoriByUnit)) {  
                $data->whereIn('kategori', $kategoriByUnit);  
            }  
        } elseif (in_array($user->role, ['admin', 'superadmin'])) {  
            // No additional filters for admin or superadmin  
        } elseif ($user->role === 'analis') {  
            $data->whereHas('assignments', function ($query) use ($user) {  
                $query->where('analis_id', $user->id_admins);  
            });  
        } else {  
            $data->where(function ($query) use ($user) {  
                $query->where('disposisi', $user->role)  
                    ->orWhere('disposisi_terbaru', $user->role);  
            });  
        }

        // Filter status analisis
        if ($this->filterStatusAnalisis) {
            $data->where('status_analisis', $this->filterStatusAnalisis);
        }

        // Filter data pelimpahan - Menghindari data dengan nilai di kedua kolom disposisi dan disposisi_terbaru
        // Data pelimpahan: memiliki nilai di kedua kolom disposisi dan disposisi_terbaru
        if ($this->filterPelimpahan) {
            $data->whereNotNull('disposisi_terbaru'); // hanya menampilkan laporan yang memiliki disposisi_terbaru
        } else {
            // Filter data biasa - hanya ada nilai di kolom disposisi
            $data->whereNull('disposisi_terbaru'); // hanya menampilkan laporan yang tidak memiliki disposisi_terbaru
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
            // Filter untuk laporan yang tidak memiliki disposisi dan disposisi_terbaru  
            $data->whereNull('disposisi')  
                ->whereNull('disposisi_terbaru'); // Both must be null  
        } elseif ($this->filterAssignment === 'either_disposition') {  
            // Filter untuk laporan yang memiliki salah satu dari disposisi atau disposisi_terbaru null  
            $data->where(function ($query) {  
                $query->whereNull('disposisi')  
                    ->orWhereNull('disposisi_terbaru'); // At least one must be null  
            });  
        }  
    
        // Filter untuk hanya menampilkan laporan dengan disposisi dan disposisi_terbaru terisi  
        // $data->where(function ($query) {  
        //     $query->whereNotNull('disposisi')  
        //         ->orWhereNotNull('disposisi_terbaru');  
        // });  
    
        // Filter berdasarkan tanggal  
        if (!empty($this->tanggal)) {  
            $data->whereDate('created_at', $this->tanggal);  
        }  
    
        // Urutkan data berdasarkan status  
        $data->orderByRaw("
            CASE
                WHEN EXISTS(SELECT 1 FROM assignments WHERE assignments.laporan_id = laporans.id) THEN 1
                ELSE 0
            END,
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

    public function updateKategoriMassal()
    {
        if (!empty($this->selected) && !empty($this->selectedKategori)) {
            Laporan::whereIn('id', $this->selected)
                ->update(['kategori' => $this->selectedKategori]);

            // Log aktivitas
            foreach ($this->selected as $laporanId) {
                Log::create([
                    'laporan_id' => $laporanId,
                    'activity' => 'Kategori diperbarui menjadi ' . $this->selectedKategori,
                    'user_id' => auth('admin')->user()->id_admins,
                ]);
            }

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

            // Log aktivitas
            foreach ($this->selected as $laporanId) {
                Log::create([
                    'laporan_id' => $laporanId,
                    'activity' => 'Disposisi diperbarui menjadi ' . $this->selectedDisposisi,
                    'user_id' => auth('admin')->user()->id_admins,
                ]);
            }

            $this->reset(['selected', 'selectAll', 'selectedDisposisi']);
            session()->flash('success', 'Disposisi berhasil diperbarui.');
        } else {
            session()->flash('error', 'Pilih disposisi dan data terlebih dahulu.');
        }
    }

    public function assignToAnalis()  
    {  
        // Check if any reports and an analyst have been selected  
        if (empty($this->selected) || empty($this->selectedAnalis)) {  
            session()->flash('error', 'Pilih analis dan data terlebih dahulu.');  
            return;  
        }  
    
        // Validate the selected analyst and optional notes  
        $this->validate([  
            'selectedAnalis' => 'required|exists:admins,id_admins',  
            'assignNotes' => 'nullable|string|max:255', // Optional notes  
        ]);  
    
        foreach ($this->selected as $laporanId) {  
            // Check if the report already has an existing assignment  
            $existingAssignment = Assignment::where('laporan_id', $laporanId)->first();  
    
            if ($existingAssignment) {  
                session()->flash('error', "Laporan #{$laporanId} sudah ditugaskan ke analis lain.");  
                continue; // Skip to the next report if already assigned  
            }  
    
            // Create a new assignment  
            Assignment::create([  
                'laporan_id' => $laporanId,  
                'analis_id' => $this->selectedAnalis,  
                'notes' => $this->assignNotes, // Optional notes  
                'assigned_by' => auth('admin')->user()->id_admins, // ID of the user assigning  
            ]);  
    
            // Send notification to the assigned analyst  
            Notification::create([  
                'assigner_id' => auth('admin')->user()->id_admins, // ID of the user assigning  
                'assignee_id' => $this->selectedAnalis, // ID of the assigned analyst  
                'laporan_id' => $laporanId, // ID of the report  
                'is_read' => false, // Set as unread  
                'message' => "telah ditugaskan kepada Anda.", // Notification message  
            ]);

            // Log aktivitas penugasan ke analis
            Log::create([
                'laporan_id' => $laporanId,
                'activity' => 'Laporan ditugaskan ke analis ' . $this->selectedAnalis,
                'user_id' => auth('admin')->user()->id_admins,
            ]);
        }  
    
        // Reset selections and show success message  
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
                    'message' => 'Pelimpahan data ke ' . $this->selectedDisposisi,     // Pesan notifikasi
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
                    'message' => 'Pelimpahan data ke ' . $this->selectedDisposisi,     // Pesan notifikasi
                    'role' => $deputiName,                                // Isi role dengan nama kedeputian
                    'is_read' => false,                                   // Status notifikasi belum dibaca
                ]);
            }

            // Menyimpan log pelimpahan
            Log::create([
                'laporan_id' => $laporanId,
                'activity' => 'Laporan dilimpahkan ke deputi ' . $deputiName, // Menggunakan variabel $deputiName secara langsung
                'user_id' => auth('admin')->user()->id_admins,
            ]);
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

    // Fungsi untuk mendapatkan role berdasarkan disposisi
    private function getRoleByDisposisi($disposisi)
    {
        return $disposisi;  
    }
    
    private function getUserIdByRole($role)  
    {  
        // Retrieve the user ID based on the role  
        return admins::where('role', $role)->first()->id_admins ?? null;  
    }  
    
    private function sendNotification($assignerId, $assigneeId, $laporanId, $message)  
    {  
        if ($assigneeId) {  
            Notification::create([  
                'assigner_id' => $assignerId,  
                'assignee_id' => $assigneeId,  
                'laporan_id' => $laporanId,  
                'is_read' => false, // Set as unread  
                'message' => $message, // Add the message for the notification  
            ]);  
        }  
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