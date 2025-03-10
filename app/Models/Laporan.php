<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'dokumen_tambahan',
        'tanggal_kejadian',
        'status',
        'tanggapan',
        'klasifikasi',
        'kategori',
        'disposisi',
        'disposisi_terbaru',
        'sumber_pengaduan',
        'lembar_kerja_analis',
        'status_analisis',
        'petugas',
        'created_at',
        'updated_at',
    ];

    // public $timestamps = false; //Nonaktifkan timestamps otomatis
    
    protected $casts = [
        'tanggal_kejadian' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $attributes = [
        'tanggapan' => 'Laporan pengaduan Saudara dalam proses verifikasi & penelaahan.',
    ];

    protected static function boot()
    {
        parent::boot();

        // Mengisi kolom disposisi secara otomatis berdasarkan kategori
        static::saving(function ($laporan) {
            if (!empty($laporan->kategori)) {
                $kategoriDeputi = self::$kategoriDeputi;
                foreach ($kategoriDeputi as $deputi => $kategoris) {
                    if (in_array($laporan->kategori, $kategoris)) {
                        $laporan->disposisi = $deputi;
                        break;
                    }
                }
            }
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

    // Deklarasi properti
    private static $kategoriKataKunci = [
        // Tambahkan semua kategori lama dan baru di sini
        'Agama' => ['agama', 'ibadah', 'rumah ibadah', 'masjid', 'gereja', 'penistaan', 'hari besar keagamaan', 'yayasan keagamaan', 'zakat', 'wakaf', 'pesantren', 'haji', 'umroh', 'toleransi', 'santri', 'iman', 'amil', 'p3ukdk'],
        'Corona Virus' => ['covid', 'corona', 'pandemi', 'vaksin', 'omicron', 'lockdown', 'ppkm', 'varian'],
        'Ekonomi dan Keuangan' => ['ekonomi', 'keuangan', 'uang', 'investasi', 'bank', 'pinjaman', 'kredit', 'tabungan', 'inflasi', 'pinjol', 'utang', 'modal usaha', 'hutang', 'bodong', 'dana', 'asuransi', 'online', 'pajak', 'modal', 'penjaminan', 'pailit', 'angsuran', 'ojk', 'rentenir', 'tagihan', 'bi checking', 'restru'],
        'Kesehatan' => ['kesehatan', 'fasilitas dan pelayanan kesehatan', 'dokter', 'puskesmas', 'obat', 'penyakit', 'vaksin', 'bpjs kesehatan', 'perawat', 'stunting', 'rumah sakit', 'organisasi profesi tenaga kesehatan', 'malpraktek', 'pasien', 'sehat', 'sakit', 'stunting', 'gizi', 'rsud', 'persalinan', 'posyandu', 'akupuntur', 'makan bergizi', 'makan gratis'],
        'Kesetaraan Gender dan Sosial Inklusif' => ['gender', 'kesetaraan', 'inklusi', 'organisasi wanita', 'difabel', 'perempuan', 'lgbt', 'waria', 'hak', 'gay', 'anak', 'ketahanan keluarga'],
        'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat' => ['ketentraman', 'ketertiban', 'perlindungan', 'keamanan', 'keributan', 'masyarakat', 'konflik', 'kerusuhan', 'kriminalitas', 'kekerasan', 'bising', 'pkl', 'liar', 'rokok'],
        'Lingkungan Hidup dan Kehutanan' => ['lingkungan', 'hutan', 'polusi','sampah','air','pencemaran','deforestasi','kehutanan','reboisasi','limbah','banjir','erosi','kerusakan','ekosistem','abrasi','udara','penghijauan','kebakaran','perhutanan sosial','sungai','tanah','lahan','sawit','ulayat','adat'],
        'Pekerjaan Umum dan Penataan Ruang' => ['pekerjaan umum', 'infrastruktur', 'jalan','jembatan','bangunan','penataan ruang','pemukiman','gedung','rtrw','bendungan','sertifikat','tanah','shm','rumah','perkebunan','irigasi','ajb','ptsl','hgb','hgu','tora','agraria','shp','sertifikat','psn','mbr','rusun','apartemen','adat','sewa', 'jalan rusak', 'fasilitas umum','proyek'],
        'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi' => ['desa','pembangunan','daerah tertinggal','transmigrasi','pedesaan','pengembangan daerah','daerah 3T','dana desa','pembangunan desa'],
        'Pendidikan dan Kebudayaan' => ['pendidikan','sekolah','guru','murid','sekolah inklusif','kebudayaan','olahraga','universitas','pelajaran','beasiswa','buku','modul','tenaga pendidikan','ujian','jambore','pramuka','ijazah','kurikulum','prestasi siswa','prestasi guru','dosen','penerimaan siswa baru','pemagangan','zonasi', 's1', 'kuliah', 'ukt', 'renovasi', 'bbh', 'kip', 'nuptk', 'tpg', 'gtk', 'tendik', 'lpdp', 'dapodik', 'pip', 'kjmu'],
        'Pertanian dan Peternakan' => ['pertanian','peternakan','tanaman','pupuk','petani','ternak','hasil panen','sapi','ayam','bibit','lahan','teknologi pertanian','produktifitas','kesejahteraan petani','nelayan','perkapalan','kesejahteraaan nelayan','kapal ikan','tambak','daging sapi','perkebunan','padi','anak buah kapal','abk','pakan ikan','KUR pertanian','KUR perikanan'],
        'Politik dan Hukum' => ['politik','hukum','peraturan','pemilu','korupsi','regulasi','pengadilan','keadilan','legislasi','partai politik','putusan pengadilan','mafia hukum','lembaga peradilan','pertanahan','parpol', 'peradilan', 'pertanahan', 'polisi', 'polres', 'jaksa', 'penipuan', 'pidana', 'kasus', 'begal', 'pungli', 'kriminal', 'aniaya', 'skck', 'mediasi', 'pungutan', 'perkara', 'polda', 'penindakan', 'polsek', 'curanmor', 'kdrt', 'hilang', 'mata elang', 'adil', 'ormas', 'scam', 'pemerasan', 'ancaman', 'hinaan', 'grasi', 'judi', 'curi', 'tipikor', 'wanprestasi'],
        'Politisasi ASN' => ['asn','politisasi asn','netralitas asn','kampanye','pegawai negeri','pns','kode etik asn','manajemen asn','pengangkatan p3k','gaji asn','honorer','mutasi','penyalahgunaan wewenang','tes cpns'],
        'Sosial dan Kesejahteraan' => ['sosial','kesejahteraan','bansos','kesejahteraan sosial','penanggulangan kemiskinan','keluarga miskin','lansia','difabel','kartu lansia','disabilitas','tunggakan spp','tebus ijazah','baznas','miskin','bantuan sosial','pkh','dtks','blt','bpjs', 'makan gratis', 'makan', 'jkn', 'subsidi', 'bpnt', 'kjp', 'kis'],
        'SP4N Lapor' => ['lapor', 'pengaduan', 'sp4n', 'tindak lanjut', 'sistem pengaduan'],
        'Energi dan Sumber Daya Alam' => ['energi','minyak','gas','pertambangan','sumber daya alam','sda','listrik','pembangkit','bbm','pln','ebt','smelter','hilirisasi', 'tambang', 'pasir'],
        'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)' => ['kekerasan','bullying','pelecehan','lembaga diklat','kampus','sekolah','pendidikan','bully','dosen','mahasiswa','siswa'],
        'Kependudukan' => ['penduduk', 'kependudukan', 'ktp', 'nik', 'domisili', 'dukcapil', 'kartu keluarga', 'pernikahan', 'akta kelahiran', 'kia'],
        'Ketenagakerjaan' => ['pekerja','migran','tenaga kerja','buruh','karyawan','phk','upah','gaji','tunjangan','pensiun','jaminan kerja','outsourcing','hubungan industrial','kesempatan kerja','cuti','bpjs ketenagakerjaan','serikat pekerja','lowongan','pengangguran','pecat', 'kerja', 'rekrutmen', 'recruitment', 'pkwt', 'putus kontrak', 'loker', 'umk', 'thr', 'pesangon', 'pekerja migran', 'ump', 'umr'],
        'Netralitas ASN' => ['asn','pns','netralitas','politik','pegawai negeri','pilkada','kampanye'],
        'Pemulihan Ekonomi Nasional' => ['pemulihan','ekonomi','nasional','program','recovery','dampak pandemi','modal usaha'],
        'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika dan Prekursor Narkotika (P4GN)' => ['narkoba','p4gn','peredaran','penyalahgunaan','narkotika','obat'],
        'Mudik' => ['mudik','peniadaan','larangan','lebaran','transportasi','ppkm','tahun baru','mudik gratis','angkutan','lalu lintas','harga tiket','macet','tiket','libur','rest area','cuti','kecelakaan','natal','tol','tuslah','diskon','online'],
        'Perairan' => ['air','laut','sungai','bendungan','pelabuhan','irigasi','IPAL','keramba jaring apung','ikan','perikanan','budidaya','kualitas air','kja','udang','tambak','ekosistem', 'pdam', 'normalisasi'],
        'Perhubungan' => ['transportasi', 'angkutan', 'jalan', 'kendaraan', 'kereta', 'bus', 'pesawat', 'ojek online', 'ojek', 'mobil', 'motor', 'kapal', 'terminal', 'lrt', 'mrt', 'bandar udara', 'pelabuhan', 'stasiun', 'halte', 'tol', 'logistik', 'paket', 'barang', 'surat', 'asuransi', 'tod', 'parkir', 'sertifikasi', 'psn', 'tiket', 'truck', 'truk'],
        'Perlindungan Konsumen' => ['konsumen', 'perlindungan', 'penipuan', 'online', 'jual', 'ecommerce', 'bajakan', 'shopping', 'belanja', 'beli', 'produk', 'harga', 'robot', 'trading', 'transfer', 'tipu', 'shop', 'teror', 'afiliasi', 'korban', 'net89', 'noop'],
        'Teknologi Informasi dan Komunikasi' => ['teknologi', 'informasi', 'komunikasi', 'internet', 'digital', 'aplikasi', 'telekomunikasi', 'bts', 'literasi', 'hardware', 'software', 'data pribadi', 'data', 'jaringan', 'sistem', 'AI', '5G', '4G', 'sambungan', 'satelit', 'keamanan', 'cloud', 'frekuensi', 'hack', 'judol', 'whatsapp'],
        'Topik Khusus' => ['khusus', 'topik', 'isu tertentu', 'spesifik', 'pajak'],
        'Topik Lainnya' => ['lainnya'],
        'Perumahan' => ['pemukiman', 'gedung', 'sertipikat', 'tanah', 'shm', 'rumah', 'ajb', 'mbr', 'rusun', 'apartemen', 'adat','sewa', 'bangunan', 'kpr'],
        'Daerah Perbatasan' => ['daerah perbatasan', 'perbatasan', 'wilayah perbatasan', '3t', 'border', 'plbn', 'lintas batas'],
        'Kepemudaan dan Olahraga' => ['pemuda', 'olahraga', 'atlet', 'cabor', 'koni'],
        'Manajemen ASN' => ['asn', 'pegawai negeri', 'manajemen', 'gaji', 'pns', 'pengangkatan', 'seleksi', 'cpns', 'p3k', 'formasi', 'pppk', 'remun', 'psikotes', 'cp3k'],
        'Keluarga Berencana' => ['kb', 'keluarga berencana', 'alat kontrasepsi'],
        'Bantuan Masyarakat' => ['tunggakan sekolah', 'modal usaha', 'bantuan', 'tunggakan spp', 'tunggakan', 'proposal', 'proposal masjid', 'tebus ijazah', 'ambil ijazah', 'gereja', 'proposal desa', 'tunggak', 'spp'],
        'Luar Negeri' => ['imigran', 'kekonsuleran', 'pengungsi', 'migran', 'deportan', 'pencari suaka', 'tppo', 'paspor', 'wna', 'tkw', 'tki', 'imigrasi', 'kitas', 'trafficking'],
        'Pariwisata dan Ekonomi Kreatif' => ['pariwisata', 'kreatif', 'wisata', 'turis', 'visa', 'turis lokal', 'turis asing', 'tiket pesawat', 'tiket masuk', 'wisata', 'akomodasi', 'hotel', 'wisatawan', 'pemandu wisata', 'souvenir', 'budaya', 'tari', 'performence', 'konser', 'musik', 'hiburan', 'film', 'entertainment', 'penyanyi', 'penari', 'pelawak', 'komedi', 'lagu', 'kreatif', 'okupansi', 'destinasi', 'desa wisata', 'cagar budaya', 'penulis', 'lukisan', 'anyaman', 'tenun', 'batik', 'atraksi', 'hospitaliti', 'trip', 'travel', 'festival'],
        'Pemberdayaan Masyarakat, Koperasi, dan UMKM' => ['umkm', 'koperasi', 'usaha kecil', 'usaha mikro', 'modal usaha', 'pemberdayaan masyarakat', 'kur', 'kredit macet', 'jaminan kur', 'usaha menengah', 'blacklist bank', 'keringanan bunga', 'penghapusan hutang', 'cicilan', 'angsuran'],
        'Industri dan Perdagangan' => ['industri', 'perdagangan', 'ekspor', 'impor', 'barang', 'online', 'beli', 'dagang', 'jual', 'jasa', 'produsen', 'distributor', 'harga', 'toko', 'koperasi', 'pemasok', 'industri', 'tekstil', 'otomotif', 'konsumen', 'mesin', 'gudang', 'logistik', 'industri pengolahan', 'restoran', 'rumah makan', 'warung', 'pabrik', 'manufaktur', 'bahan baku', 'pasar', 'retail', 'supermarket', 'usaha', 'grosir', 'harga', 'bahan pokok', 'monopoli', 'kuota ekspor', 'dumping', 'e-commerce', 'bea masuk', 'profit', 'komoditi', 'komoditas', 'produk', 'perindag'],
        'Penanggulangan Bencana' => ['bencana', 'gempa', 'banjir', 'kebakaran', 'gunung meletus', 'tsunami', 'tanah longsor', 'relokasi', 'hunian tetap', 'hunian sementara', 'bnpb', 'rehabilitasi', 'rekonstruksi', 'bantuan korban bencana', 'bpbd', 'dana siap pakai', 'early warning system', 'kebakaran hutan dan lahan', 'pasca bencana', 'perubahan iklim', 'dana hibah', 'erupsi', 'mitigasi bencana', 'tanggap darurat', 'desa tangguh bencana', 'logistik bantuan', 'kekeringan', 'bencana non alam', 'pra bencana', 'krisis air', 'tpa'],
        'Pertanahan' => ['tanah', 'agraria', 'sertifikat', 'pembebasan lahan', 'pungutan', 'pungli', 'tanah', 'bangunan', 'bpn', 'waris'],
        'Pelayanan Publik' => ['samsat', 'pelayanan', 'sim', 'birokrasi'],
        'TNI' => ['tni'],
        'Polri' => ['polri'],
        'Perpajakan' => ['pajak', 'tax'],
        'Lainnya' => [],
    ];

    // Daftar kata kunci dan kategori
    private static $kategoriSP4NLapor = [
        'Agama' => ['agama', 'ibadah', 'rumah ibadah', 'masjid', 'gereja', 'penistaan', 'hari besar keagamaan', 'yayasan keagamaan', 'zakat', 'wakaf', 'pesantren', 'haji', 'umroh', 'toleransi', 'santri', 'iman', 'amil', 'p3ukdk'],
        'Corona Virus' => ['covid', 'corona', 'pandemi', 'vaksin', 'omicron', 'lockdown', 'ppkm', 'varian'],
        'Ekonomi dan Keuangan' => ['ekonomi', 'keuangan', 'uang', 'investasi', 'bank', 'pinjaman', 'kredit', 'tabungan', 'inflasi', 'pinjol', 'utang', 'modal usaha', 'hutang', 'bodong', 'dana', 'asuransi', 'online', 'pajak', 'modal', 'penjaminan', 'pailit', 'angsuran', 'ojk', 'rentenir', 'tagihan', 'bi checking', 'restru'],
        'Kesehatan' => ['kesehatan', 'fasilitas dan pelayanan kesehatan', 'dokter', 'puskesmas', 'obat', 'penyakit', 'vaksin', 'bpjs kesehatan', 'perawat', 'stunting', 'rumah sakit', 'organisasi profesi tenaga kesehatan', 'malpraktek', 'pasien', 'sehat', 'sakit', 'stunting', 'gizi', 'rsud', 'persalinan', 'posyandu', 'akupuntur', 'makan bergizi', 'makan gratis'],
        'Kesetaraan Gender dan Sosial Inklusif' => ['gender', 'kesetaraan', 'inklusi', 'organisasi wanita', 'difabel', 'perempuan', 'lgbt', 'waria', 'hak', 'gay', 'anak', 'ketahanan keluarga'],
        'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat' => ['ketentraman', 'ketertiban', 'perlindungan', 'keamanan', 'keributan', 'masyarakat', 'konflik', 'kerusuhan', 'kriminalitas', 'kekerasan', 'bising', 'pkl', 'liar', 'rokok'],
        'Lingkungan Hidup dan Kehutanan' => ['lingkungan', 'hutan', 'polusi','sampah','air','pencemaran','deforestasi','kehutanan','reboisasi','limbah','banjir','erosi','kerusakan','ekosistem','abrasi','udara','penghijauan','kebakaran','perhutanan sosial','sungai','tanah','lahan','sawit','ulayat','adat'],
        'Pekerjaan Umum dan Penataan Ruang' => ['pekerjaan umum', 'infrastruktur', 'jalan','jembatan','bangunan','penataan ruang','pemukiman','gedung','rtrw','bendungan','sertifikat','tanah','shm','rumah','perkebunan','irigasi','ajb','ptsl','hgb','hgu','tora','agraria','shp','sertifikat','psn','mbr','rusun','apartemen','adat','sewa', 'jalan rusak', 'fasilitas umum','proyek'],
        'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi' => ['desa','pembangunan','daerah tertinggal','transmigrasi','pedesaan','pengembangan daerah','daerah 3T','dana desa','pembangunan desa'],
        'Pendidikan dan Kebudayaan' => ['pendidikan','sekolah','guru','murid','sekolah inklusif','kebudayaan','olahraga','universitas','pelajaran','beasiswa','buku','modul','tenaga pendidikan','ujian','jambore','pramuka','ijazah','kurikulum','prestasi siswa','prestasi guru','dosen','penerimaan siswa baru','pemagangan','zonasi', 's1', 'kuliah', 'ukt', 'renovasi', 'bbh', 'kip', 'nuptk', 'tpg', 'gtk', 'tendik', 'lpdp', 'dapodik', 'pip', 'kjmu'],
        'Pertanian dan Peternakan' => ['pertanian','peternakan','tanaman','pupuk','petani','ternak','hasil panen','sapi','ayam','bibit','lahan','teknologi pertanian','produktifitas','kesejahteraan petani','nelayan','perkapalan','kesejahteraaan nelayan','kapal ikan','tambak','daging sapi','perkebunan','padi','anak buah kapal','abk','pakan ikan','KUR pertanian','KUR perikanan'],
        'Politik dan Hukum' => ['politik','hukum','peraturan','pemilu','korupsi','regulasi','pengadilan','keadilan','legislasi','partai politik','putusan pengadilan','mafia hukum','lembaga peradilan','pertanahan','parpol', 'peradilan', 'pertanahan', 'polisi', 'polres', 'jaksa', 'penipuan', 'pidana', 'kasus', 'begal', 'pungli', 'kriminal', 'aniaya', 'skck', 'mediasi', 'pungutan', 'perkara', 'polda', 'penindakan', 'polsek', 'curanmor', 'kdrt', 'hilang', 'mata elang', 'adil', 'ormas', 'scam', 'pemerasan', 'ancaman', 'hinaan', 'grasi', 'judi', 'curi', 'tipikor', 'wanprestasi'],
        'Politisasi ASN' => ['asn','politisasi asn','netralitas asn','kampanye','pegawai negeri','pns','kode etik asn','manajemen asn','pengangkatan p3k','gaji asn','honorer','mutasi','penyalahgunaan wewenang','tes cpns'],
        'Sosial dan Kesejahteraan' => ['sosial','kesejahteraan','bansos','kesejahteraan sosial','penanggulangan kemiskinan','keluarga miskin','lansia','difabel','kartu lansia','disabilitas','tunggakan spp','tebus ijazah','baznas','miskin','bantuan sosial','pkh','dtks','blt','bpjs', 'makan gratis', 'makan', 'jkn', 'subsidi', 'bpnt', 'kjp', 'kis'],
        'SP4N Lapor' => ['lapor', 'pengaduan', 'sp4n', 'tindak lanjut', 'sistem pengaduan'],
        'Energi dan Sumber Daya Alam' => ['energi','minyak','gas','pertambangan','sumber daya alam','sda','listrik','pembangkit','bbm','pln','ebt','smelter','hilirisasi', 'tambang', 'pasir'],
        'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)' => ['kekerasan','bullying','pelecehan','lembaga diklat','kampus','sekolah','pendidikan','bully','dosen','mahasiswa','siswa'],
        'Kependudukan' => ['penduduk', 'kependudukan', 'ktp', 'nik', 'domisili', 'dukcapil', 'kartu keluarga', 'pernikahan', 'akta kelahiran', 'kia'],
        'Ketenagakerjaan' => ['pekerja','migran','tenaga kerja','buruh','karyawan','phk','upah','gaji','tunjangan','pensiun','jaminan kerja','outsourcing','hubungan industrial','kesempatan kerja','cuti','bpjs ketenagakerjaan','serikat pekerja','lowongan','pengangguran','pecat', 'kerja', 'rekrutmen', 'recruitment', 'pkwt', 'putus kontrak', 'loker', 'umk', 'thr', 'pesangon', 'pekerja migran', 'ump', 'umr'],
        'Netralitas ASN' => ['asn','pns','netralitas','politik','pegawai negeri','pilkada','kampanye'],
        'Pemulihan Ekonomi Nasional' => ['pemulihan','ekonomi','nasional','program','recovery','dampak pandemi','modal usaha'],
        'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika dan Prekursor Narkotika (P4GN)' => ['narkoba','p4gn','peredaran','penyalahgunaan','narkotika','obat'],
        'Mudik' => ['mudik','peniadaan','larangan','lebaran','transportasi','ppkm','tahun baru','mudik gratis','angkutan','lalu lintas','harga tiket','macet','tiket','libur','rest area','cuti','kecelakaan','natal','tol','tuslah','diskon','online'],
        'Perairan' => ['air','laut','sungai','bendungan','pelabuhan','irigasi','IPAL','keramba jaring apung','ikan','perikanan','budidaya','kualitas air','kja','udang','tambak','ekosistem', 'pdam', 'normalisasi'],
        'Perhubungan' => ['transportasi', 'angkutan', 'jalan', 'kendaraan', 'kereta', 'bus', 'pesawat', 'ojek online', 'ojek', 'mobil', 'motor', 'kapal', 'terminal', 'lrt', 'mrt', 'bandar udara', 'pelabuhan', 'stasiun', 'halte', 'tol', 'logistik', 'paket', 'barang', 'surat', 'asuransi', 'tod', 'parkir', 'sertifikasi', 'psn', 'tiket', 'truck', 'truk'],
        'Perlindungan Konsumen' => ['konsumen', 'perlindungan', 'penipuan', 'online', 'jual', 'ecommerce', 'bajakan', 'shopping', 'belanja', 'beli', 'produk', 'harga', 'robot', 'trading', 'transfer', 'tipu', 'shop', 'teror', 'afiliasi', 'korban', 'net89', 'noop'],
        'Teknologi Informasi dan Komunikasi' => ['teknologi', 'informasi', 'komunikasi', 'internet', 'digital', 'aplikasi', 'telekomunikasi', 'bts', 'literasi', 'hardware', 'software', 'data pribadi', 'data', 'jaringan', 'sistem', 'AI', '5G', '4G', 'sambungan', 'satelit', 'keamanan', 'cloud', 'frekuensi', 'hack', 'judol', 'whatsapp'],
        'Topik Khusus' => ['khusus', 'topik', 'isu tertentu', 'spesifik', 'pajak'],
        'Topik Lainnya' => ['lainnya'],
    ];

    private static $kategoriBaru = [
        'Perumahan' => ['pemukiman', 'gedung', 'sertipikat', 'tanah', 'shm', 'rumah', 'ajb', 'mbr', 'rusun', 'apartemen', 'adat','sewa', 'bangunan', 'kpr'],
        'Daerah Perbatasan' => ['daerah perbatasan', 'perbatasan', 'wilayah perbatasan', '3t', 'border', 'plbn', 'lintas batas'],
        'Kepemudaan dan Olahraga' => ['pemuda', 'olahraga', 'atlet', 'cabor', 'koni'],
        'Manajemen ASN' => ['asn', 'pegawai negeri', 'manajemen', 'gaji', 'pns', 'pengangkatan', 'seleksi', 'cpns', 'p3k', 'formasi', 'pppk', 'remun', 'psikotes', 'cp3k'],
        'Keluarga Berencana' => ['kb', 'keluarga berencana', 'alat kontrasepsi'],
        'Bantuan Masyarakat' => ['tunggakan sekolah', 'modal usaha', 'bantuan', 'tunggakan spp', 'tunggakan', 'proposal', 'proposal masjid', 'tebus ijazah', 'ambil ijazah', 'gereja', 'proposal desa', 'tunggak', 'spp'],
        'Luar Negeri' => ['imigran', 'kekonsuleran', 'pengungsi', 'migran', 'deportan', 'pencari suaka', 'tppo', 'paspor', 'wna', 'tkw', 'tki', 'imigrasi', 'kitas', 'trafficking'],
        'Pariwisata dan Ekonomi Kreatif' => ['pariwisata', 'kreatif', 'wisata', 'turis', 'visa', 'turis lokal', 'turis asing', 'tiket pesawat', 'tiket masuk', 'wisata', 'akomodasi', 'hotel', 'wisatawan', 'pemandu wisata', 'souvenir', 'budaya', 'tari', 'performence', 'konser', 'musik', 'hiburan', 'film', 'entertainment', 'penyanyi', 'penari', 'pelawak', 'komedi', 'lagu', 'kreatif', 'okupansi', 'destinasi', 'desa wisata', 'cagar budaya', 'penulis', 'lukisan', 'anyaman', 'tenun', 'batik', 'atraksi', 'hospitaliti', 'trip', 'travel', 'festival'],
        'Pemberdayaan Masyarakat, Koperasi, dan UMKM' => ['umkm', 'koperasi', 'usaha kecil', 'usaha mikro', 'modal usaha', 'pemberdayaan masyarakat', 'kur', 'kredit macet', 'jaminan kur', 'usaha menengah', 'blacklist bank', 'keringanan bunga', 'penghapusan hutang', 'cicilan', 'angsuran'],
        'Industri dan Perdagangan' => ['industri', 'perdagangan', 'ekspor', 'impor', 'barang', 'online', 'beli', 'dagang', 'jual', 'jasa', 'produsen', 'distributor', 'harga', 'toko', 'koperasi', 'pemasok', 'industri', 'tekstil', 'otomotif', 'konsumen', 'mesin', 'gudang', 'logistik', 'industri pengolahan', 'restoran', 'rumah makan', 'warung', 'pabrik', 'manufaktur', 'bahan baku', 'pasar', 'retail', 'supermarket', 'usaha', 'grosir', 'harga', 'bahan pokok', 'monopoli', 'kuota ekspor', 'dumping', 'e-commerce', 'bea masuk', 'profit', 'komoditi', 'komoditas', 'produk', 'perindag'],
        'Penanggulangan Bencana' => ['bencana', 'gempa', 'banjir', 'kebakaran', 'gunung meletus', 'tsunami', 'tanah longsor', 'relokasi', 'hunian tetap', 'hunian sementara', 'bnpb', 'rehabilitasi', 'rekonstruksi', 'bantuan korban bencana', 'bpbd', 'dana siap pakai', 'early warning system', 'kebakaran hutan dan lahan', 'pasca bencana', 'perubahan iklim', 'dana hibah', 'erupsi', 'mitigasi bencana', 'tanggap darurat', 'desa tangguh bencana', 'logistik bantuan', 'kekeringan', 'bencana non alam', 'pra bencana', 'krisis air', 'tpa'],
        'Pertanahan' => ['tanah', 'agraria', 'sertifikat', 'pembebasan lahan', 'pungutan', 'pungli', 'tanah', 'bangunan', 'bpn', 'waris'],
        'Pelayanan Publik' => ['samsat', 'pelayanan', 'sim', 'birokrasi'],
        'TNI' => ['tni'],
        'Polri' => ['polri'],
        'Perpajakan' => ['pajak', 'tax'],
        'Lainnya' => [],
    ];

    public static function getKategoriSP4NLapor()
    {
        return array_keys(self::$kategoriSP4NLapor);
    }

    public static function getKategoriBaru()
    {
        return array_keys(self::$kategoriBaru);
    }

    public static function getKategoriDeputi2()
    {
        // Mendefinisikan kategori untuk setiap deputi secara manual
        $kategoriDeputi = [];

        // Deputi 1
        $kategoriDeputi['deputi_1'] = [
            'Ekonomi dan Keuangan',
            'Lingkungan Hidup dan Kehutanan',
            'Pekerjaan Umum dan Penataan Ruang',
            'Pertanian dan Peternakan',
            'Pemulihan Ekonomi Nasional',
            'Energi dan Sumber Daya Alam',
            'Mudik',
            'Perairan',
            'Perhubungan',
            'Teknologi Informasi dan Komunikasi',
            'Perlindungan Konsumen',
            'Pariwisata dan Ekonomi Kreatif',
            'Industri dan Perdagangan',
            'Perumahan',
            'Perpajakan'
        ];

        // Deputi 2
        $kategoriDeputi['deputi_2'] = [
            'Agama',
            'Corona Virus',
            'Kesehatan',
            'Kesetaraan Gender dan Sosial Inklusif',
            'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi',
            'Pendidikan dan Kebudayaan',
            'Sosial dan Kesejahteraan',
            'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)',
            'Penanggulangan Bencana',
            'Ketenagakerjaan',
            'Pemberdayaan Masyarakat, Koperasi, dan UMKM',
            'Kepemudaan dan Olahraga',
            'Keluarga Berencana',
            'Pembangunan Keluarga'
        ];

        // Deputi 3
        $kategoriDeputi['deputi_3'] = [
            'Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat',
            'Politik dan Hukum',
            'Politisasi ASN',
            'SP4N Lapor',
            'Netralitas ASN',
            'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika dan Prekursor Narkotika (P4GN)',
            'Manajemen ASN',
            'Luar Negeri',
            'Pertanahan',
            'Daerah Perbatasan',
            'Pelayanan Publik',
            'TNI',
            'Polri',
            'Kependudukan'
        ];

        // Deputi 4
        $kategoriDeputi['deputi_4'] = [
            'Topik Khusus',
            'Topik Lainnya',
            'Bantuan Masyarakat'
        ];

        return $kategoriDeputi;
    }

    private static $kategoriDeputi = [
        'deputi_1' => ['Ekonomi dan Keuangan', 'Lingkungan Hidup dan Kehutanan', 'Pekerjaan Umum dan Penataan Ruang', 'Pertanian dan Peternakan', 'Pemulihan Ekonomi Nasional', 'Energi dan Sumber Daya Alam', 'Mudik', 'Perairan', 'Perhubungan', 'Teknologi Informasi dan Komunikasi', 'Perlindungan Konsumen', 'Pariwisata dan Ekonomi Kreatif', 'Industri dan Perdagangan', 'Perumahan', 'Perpajakan'],
        'deputi_2' => ['Agama', 'Corona Virus', 'Kesehatan', 'Kesetaraan Gender dan Sosial Inklusif', 'Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi', 'Pendidikan dan Kebudayaan', 'Sosial dan Kesejahteraan', 'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)', 'Ketenagakerjaan', 'Kependudukan', 'Pemberdayaan Masyarakat, Koperasi, dan UMKM', 'Kepemudaan dan Olahraga', 'Keluarga Berencana', 'Penanggulangan Bencana', 'Pembangunan Keluarga'],
        'deputi_3' => ['Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat','Politik dan Hukum', 'Politisasi ASN', 'SP4N Lapor', 'Netralitas ASN', 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika dan Prekursor Narkotika (P4GN)', 'Manajemen ASN', 'Luar Negeri', 'Pertanahan', 'Daerah Perbatasan', 'Pelayanan Publik', 'TNI', 'Polri', 'Kependudukan'],
        'deputi_4' => ['Topik Khusus', 'Topik Lainnya', 'Bantuan Masyarakat'],
    ];

    private static $kategoriUnit = [
        'Asisten Deputi Ekonomi, Keuangan, dan Transformasi Digital' => ['Ekonomi dan Keuangan', 'Pemulihan Ekonomi Nasional', 'Teknologi Informasi dan Komunikasi', 'Perpajakan'],
        'Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif' => ['Perlindungan Konsumen', 'Pariwisata dan Ekonomi Kreatif', 'Industri dan Perdagangan'],
        'Asisten Deputi Infrastruktur, Sumber Daya Alam, dan Pembangunan Kewilayahan' => ['Lingkungan Hidup dan Kehutanan', 'Pekerjaan Umum dan Penataan Ruang', 'Pertanian dan Peternakan', 'Energi dan Sumber Daya Alam', 'Mudik', 'Perairan', 'Perhubungan', 'Perumahan'],
        'Asisten Deputi Pengentasan Kemiskinan dan Pembangunan Desa' => ['Pembangunan Desa, Daerah Tertinggal, dan Transmigrasi', 'Sosial dan Kesejahteraan'],
        'Asisten Deputi Kesehatan, Gizi, dan Pembangunan Keluarga' => ['Corona Virus', 'Kesehatan', 'Keluarga Berencana', 'Pembangunan Keluarga', 'Kesetaraan Gender dan Sosial Inklusif'],
        'Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana' => ['Pemberdayaan Masyarakat, Koperasi, dan UMKM', 'Penanggulangan Bencana', 'Ketenagakerjaan'],
        'Asisten Deputi Pendidikan, Agama, Kebudayaan, Pemuda, dan Olahraga' => ['Agama', 'Pendidikan dan Kebudayaan', 'Kekerasan di Satuan Pendidikan (Sekolah, Kampus, Lembaga Khusus)', 'Kepemudaan dan Olahraga'],
        'Asisten Deputi Hubungan Luar Negeri dan Pertahanan' => ['Luar Negeri', 'TNI'],
        'Asisten Deputi Politik, Keamanan, Hukum, dan Hak Asasi Manusia' => ['Ketentraman, Ketertiban Umum, dan Perlindungan Masyarakat', 'Politik dan Hukum', 'Pencegahan dan Pemberantasan Penyalahgunaan dan Peredaran Gelap Narkotika dan Prekursor Narkotika (P4GN)', 'Pertanahan', 'Polri'],
        'Asisten Deputi Tata Kelola Pemerintahan dan Percepatan Pembangunan Daerah' => ['SP4N Lapor', 'Manajemen ASN', 'Pelayanan Publik', 'Politisasi ASN', 'Netralitas ASN', 'Daerah Perbatasan', 'Kependudukan'],
        'Biro Perencanaan dan Keuangan' => ['Topik Khusus', 'Topik Lainnya', 'Bantuan Masyarakat'],
        'Biro Tata Usaha dan Sumber Daya Manusia' => [],
        'Biro Umum' => [],
        'Biro Protokol dan Kerumahtanggaan' => [],
        'Biro Pers, Media, dan Informasi' => [],
    ];

    public static function tentukanKategoriDanDeputi($judul)
    {
        $judul = strtolower($judul); // Ubah judul ke huruf kecil
        $kategoriScores = [];

        // Gabungkan semua kategori SP4N Lapor dan Kategori Baru
        $gabunganKategori = array_merge(self::$kategoriSP4NLapor, self::$kategoriBaru);

        // Hitung skor untuk setiap kategori berdasarkan kata kunci
        foreach ($gabunganKategori as $kategori => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (stripos($judul, $keyword) !== false) {
                    $score++;
                }
            }
            $kategoriScores[$kategori] = $score;
        }

        // Tentukan kategori dengan skor tertinggi
        $kategori = 'Lainnya';
        $maxScore = max($kategoriScores);
        if ($maxScore > 0) {
            $kategori = array_search($maxScore, $kategoriScores);
        }

        // Tentukan deputi berdasarkan kategori
        $deputi = null;
        foreach (self::$kategoriDeputi as $key => $categories) {
            if (in_array($kategori, $categories)) {
                $deputi = $key;
                break;
            }
        }

        return ['kategori' => $kategori, 'deputi' => $deputi];
    }

    public static function getKategoriKataKunci()
    {
        return self::$kategoriKataKunci;
    }

    public static function getKategoriDeputi()
    {
        return array_keys(self::$kategoriDeputi);
    }

    public static function getKategoriByUnit($unit)
    {
        return self::$kategoriUnit[$unit] ?? [];
    }

    // Helper untuk mendapatkan data sesuai kategori atau disposisi
    public function scopeByDeputi($query, $role)
    {
        return $query->where('disposisi', $role);
    }

    public function assignments()
    {
        // Menggunakan hasMany karena satu laporan bisa memiliki banyak assignment
        return $this->hasMany(Assignment::class, 'laporan_id');
    }

    public function assignedTo()
    {
        // Anda bisa menggunakan relasi hasMany untuk mendapatkan daftar analis yang ditugaskan ke laporan
        return $this->hasManyThrough(admins::class, Assignment::class, 'laporan_id', 'id_admins', 'id', 'analis_id');
    }

    public function notifications()  
    {  
        return $this->hasMany(Notification::class, 'laporan_id');  
    }

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class, 'laporan_id');
    }

    public function scopeFilterKategori($query, $filterKategori)
    {
        if (!empty($filterKategori)) {
            $query->where('kategori', $filterKategori);
        }
        return $query;
    }

    public function scopeFilterStatus($query, $filterStatus)
    {
        if (!empty($filterStatus)) {
            $query->where('status', $filterStatus);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('nomor_tiket', 'like', '%' . $search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('judul', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }
}