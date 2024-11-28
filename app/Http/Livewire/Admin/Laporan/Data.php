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
        $user = auth()->guard('admin')->user();

        // Jika user adalah admin, tampilkan semua kategori
        $kategori = [
            'Agama', 'Corona Virus', 'Ekonomi dan Keuangan', 'Kesehatan',
            'Kesetaraan Gender dan Sosial Inklusif', 'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat',
            'Lingkungan Hidup dan Kehutanan', 'Pekerjaan Umum dan Penataan Ruang',
            'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi', 'Pendidikan dan Kebudayaan',
            'Pertanian dan Peternakan', 'Politik dan Hukum', 'Politisasi ASN',
            'Sosial dan Kesejahteraan', 'SP4N Lapor', 'Energi dan SDA',
            'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)', 'Kependudukan',
            'Ketenagakerjaan', 'Netralitas ASN', 'Pemulihan Ekonomi Nasional',
            'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika (P4GN)',
            'Peniadaan Mudik', 'Perairan', 'Perhubungan', 'Perlindungan Konsumen',
            'Teknologi Informasi dan Komunikasi', 'Topik Khusus', 'Lainnya'
        ];

        // Query data laporan
        $data = Laporan::query();

        // Jika role adalah Deputi, filter berdasarkan disposisi
        if ($user->role !== 'admin') {
            $data->where('disposisi', $user->role); // Filter data hanya untuk disposisi Deputi
        }
        // Pencarian
        if (!empty($this->search)) {
            $data->where(function ($query) {
                $query->where('nomor_tiket', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%')
                    ->orWhere('judul', 'like', '%' . $this->search . '%');
            });
        }

        // Filter kategori (opsional, hanya jika dipilih)
        if (!empty($this->filterKategori)) {
            $data->where('kategori', $this->filterKategori);
        }

        // Paginate data
        $data = $data->orderBy('created_at', 'desc')->paginate($this->pages);

        return view('livewire.admin.laporan.data', [
            'data' => $data,
            'kategori' => $kategori, // Kirim kategori ke view
        ]);
    }
}
