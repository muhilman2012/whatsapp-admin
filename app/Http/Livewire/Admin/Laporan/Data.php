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
    public $filterKategori = '';
    public $sortField = 'created_at'; // Default field untuk sorting
    public $sortDirection = 'asc'; // Default direction untuk sorting
    public $selectedData = [];
    public $selectedKategori = null;
    public $selectedDisposisi = null;
    public $selectAll = false; // Untuk status checkbox "Select All"
    public $selected = [];    // Array ID laporan yang dipilih


    protected $listeners = ["deleteAction" => "delete"];

    protected $queryString = ['search', 'filterKategori', 'sortField', 'sortDirection'];

    public function mount()
    {
        $this->pages = 25;

        // Terima filter kategori dari URL
        $this->filterKategori = request()->get('filterKategori', '');
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

        // Query data laporan
        $data = Laporan::query();

        // Filter berdasarkan role pengguna (admin atau deputi)
        if ($user->role !== 'admin') {
            $data->where('disposisi', $user->role); // Filter berdasarkan disposisi
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

        // Paginate data
        $data = $data->orderBy('created_at', 'desc')->paginate($this->pages);

        return view('livewire.admin.laporan.data', [
            'data' => $data,
            'kategoriSP4NLapor' => $kategoriSP4NLapor,
            'kategoriBaru' => $kategoriBaru,
            'namaDeputi' => $namaDeputi,
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
}
