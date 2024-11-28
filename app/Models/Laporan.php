<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans';

    protected $fillable = [
        'nomor_tiket',
        'nama_lengkap',
        'nik',
        'nomor_pengadu',
        'email',
        'jenis_kelamin',
        'alamat_lengkap',
        'judul',
        'detail',
        'lokasi',
        'dokumen_pendukung',
        'tanggal_kejadian',
        'status',
        'tanggapan',
        'klasifikasi',
        'kategori',
        'disposisi',
        'sumber_pengaduan',
    ];

    public $timestamps = false; // Nonaktifkan timestamps otomatis
    
    protected $casts = [
        'tanggal_kejadian' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $attributes = [
        'tanggapan' => 'Laporan pengaduan Anda dalam proses verifikasi & penelaahan, sesuai ketentuan akan dilakukan dalam 14 (empat belas) hari kerja sejak laporan lengkap diterima.',
    ];

    protected static function boot()
    {
        parent::boot();

        // Automasi kategori dan disposisi saat data dibuat
        static::creating(function ($laporan) {
            $result = self::tentukanKategoriDanDeputi($laporan->judul); // Tentukan kategori dan disposisi
            $laporan->kategori = $result['kategori']; // Tetapkan kategori
            $laporan->disposisi = $result['deputi']; // Tetapkan disposisi
            $laporan->deadline = now()->addDays(20)->format('Y-m-d'); // Tetapkan deadline 20 hari
        });

        // Automasi kategori dan disposisi saat data diperbarui
        static::updating(function ($laporan) {
            $result = self::tentukanKategoriDanDeputi($laporan->judul); // Tentukan kategori dan disposisi
            $laporan->kategori = $result['kategori']; // Perbarui kategori
            $laporan->disposisi = $result['deputi']; // Perbarui disposisi
        });
    }

    public function getSisaHariAttribute()
    {
        $deadline = $this->created_at->addDays(20); // Deadline adalah 20 hari setelah created_at
        $hariTersisa = now()->diffInDays($deadline, false); // Hitung selisih dalam hari (bisa negatif)

        if ($hariTersisa > 0) {
            return "$hariTersisa hari lagi";
        } elseif ($hariTersisa === 0) {
            return "Hari ini";
        } else {
            return "Terlambat " . abs($hariTersisa) . " hari";
        }
    }

    // Fungsi untuk menentukan kategori dan Deputi
    public static function tentukanKategoriDanDeputi($judul)
    {
        // Daftar kata kunci dan kategori
        $kategoriKataKunci = [
            'Agama' => ['agama', 'masjid', 'gereja', 'ibadah', 'puasa', 'haji', 'zakat', 'umat', 'keagamaan'],
            'Corona Virus' => ['covid', 'corona', 'pandemi', 'vaksin', 'omicron', 'lockdown', 'ppkm', 'varian'],
            'Ekonomi dan Keuangan' => ['ekonomi', 'keuangan', 'uang', 'investasi', 'bank', 'pinjaman', 'kredit', 'tabungan', 'inflasi', 'pinjol'],
            'Kesehatan' => ['kesehatan', 'rumah sakit', 'dokter', 'puskesmas', 'obat', 'penyakit', 'vaksinasi', 'bpjs', 'perawatan'],
            'Kesetaraan Gender dan Sosial Inklusif' => ['gender', 'kesetaraan', 'inklusi', 'wanita', 'difabel', 'perempuan', 'lgbtq', 'kesempatan', 'hak'],
            'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat' => ['ketentraman', 'tertib', 'perlindungan', 'keamanan', 'keributan', 'masyarakat', 'konflik'],
            'Lingkungan Hidup dan Kehutanan' => ['lingkungan', 'hutan', 'polusi', 'sampah', 'air', 'pencemaran', 'deforestasi', 'kehutanan', 'reboisasi'],
            'Pekerjaan Umum dan Penataan Ruang' => ['pekerjaan umum', 'infrastruktur', 'jalan', 'jembatan', 'bangunan', 'penataan ruang', 'pemukiman'],
            'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi' => ['desa', 'pembangunan', 'daerah tertinggal', 'transmigrasi', 'pedesaan', 'pengembangan daerah'],
            'Pendidikan dan Kebudayaan' => ['pendidikan', 'sekolah', 'guru', 'murid', 'siswa', 'kebudayaan', 'universitas', 'pelajaran', 'beasiswa'],
            'Pertanian dan Peternakan' => ['pertanian', 'peternakan', 'tanaman', 'pupuk', 'petani', 'ternak', 'hasil panen', 'sapi', 'ayam'],
            'Politik dan Hukum' => ['politik', 'hukum', 'peraturan', 'pemilu', 'korupsi', 'regulasi', 'pengadilan', 'keadilan', 'legislasi'],
            'Politisasi ASN' => ['asn', 'politisasi', 'netralitas', 'kampanye', 'pegawai negeri', 'pns'],
            'Sosial dan Kesejahteraan' => ['sosial', 'kesejahteraan', 'bansos', 'kesejahteraan sosial', 'program pemerintah', 'keluarga'],
            'SP4N Lapor' => ['lapor', 'pengaduan', 'sp4n', 'tindak lanjut', 'sistem pengaduan'],
            'Energi dan SDA' => ['energi', 'minyak', 'gas', 'pertambangan', 'sumber daya alam', 'listrik', 'pembangkit', 'bbm'],
            'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)' => ['kekerasan', 'bullying', 'pelecehan', 'perundungan', 'kampus', 'sekolah', 'pendidikan'],
            'Kependudukan' => ['penduduk', 'kependudukan', 'ktp', 'nik', 'keluarga', 'domisili', 'data', 'dukcapil'],
            'Ketenagakerjaan' => ['pekerja', 'ketenagakerjaan', 'tenaga kerja', 'buruh', 'karyawan', 'phk', 'upah', 'gaji'],
            'Netralitas ASN' => ['asn', 'netralitas', 'pegawai negeri', 'pilkada', 'kampanye', 'pns', 'politik'],
            'Pemulihan Ekonomi Nasional' => ['pemulihan', 'ekonomi', 'nasional', 'program', 'recovery', 'dampak pandemi'],
            'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika dan Prekursor Narkotika (P4GN)' => ['narkoba', 'p4gn', 'peredaran', 'penyalahgunaan', 'narkotika', 'obat'],
            'Peniadaan Mudik' => ['mudik', 'peniadaan', 'larangan', 'lebaran', 'transportasi', 'ppkm'],
            'Perairan' => ['air', 'laut', 'sungai', 'bendungan', 'pelabuhan', 'irigasi'],
            'Perhubungan' => ['transportasi', 'angkutan', 'jalan', 'kendaraan', 'kereta', 'bus', 'pesawat'],
            'Perlindungan Konsumen' => ['konsumen', 'perlindungan', 'penipuan', 'online', 'jual beli', 'e-commerce'],
            'Teknologi Informasi dan Komunikasi' => ['teknologi', 'informasi', 'komunikasi', 'internet', 'digital', 'aplikasi', 'telekomunikasi'],
            'Topik Khusus' => ['khusus', 'topik', 'isu tertentu', 'spesifik'],
            'Lainnya' => [], // Biarkan kosong jika kategori tidak cocok
        ];        

        // Daftar kategori untuk setiap Deputi
        $kategoriDeputi = [
            'deputi_1' => ['Ekonomi dan Keuangan', 'Pekerjaan Umum dan Penataan Ruang', 'Pemulihan Ekonomi Nasional', 'Energi dan SDA', 'Perhubungan', 'Teknologi Informasi dan Komunikasi', 'Perlindungan Konsumen'],
            'deputi_2' => ['Kesehatan', 'Lingkungan Hidup dan Kehutanan', 'Pendidikan dan Kebudayaan', 'Sosial dan Kesejahteraan', 'Ketenagakerjaan', 'Kesetaraan Gender dan Sosial Inklusif', 'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi', 'Kependudukan'],
            'deputi_3' => ['Politisasi ASN', 'Netralitas ASN', 'SP4N Lapor', 'Administrasi Pemerintahan', 'Topik Khusus'],
            'deputi_4' => ['Politik dan Hukum', 'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat', 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika (P4GN)', 'Agama', 'Kekerasan di Satuan Pendidikan', 'Peniadaan Mudik'],
        ];

        // Tentukan kategori
        $kategori = null;
        foreach ($kategoriKataKunci as $key => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains(strtolower($judul), strtolower($keyword))) {
                    $kategori = $key;
                    break 2;
                }
            }
        }

        // Tentukan Deputi berdasarkan kategori
        $deputi = null;
        if ($kategori) {
            foreach ($kategoriDeputi as $key => $categories) {
                if (in_array($kategori, $categories)) {
                    $deputi = $key;
                    break;
                }
            }
        }

        return ['kategori' => $kategori, 'deputi' => $deputi];
    }

    // Boot untuk otomatisasi kategori dan disposisi

    // Helper untuk mendapatkan data sesuai kategori atau disposisi
    public function scopeByDeputi($query, $role)
    {
        return $query->where('disposisi', $role);
    }
}
