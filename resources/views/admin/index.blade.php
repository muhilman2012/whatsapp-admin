@extends('admin.layouts.panel')

@section('head')
    <title>LaporMasWapres! - Dashboard Admin</title>
    <style>
        /* Ukuran tetap untuk semua kartu */
        .kategori-card {
            height: 150px; /* Tinggi tetap */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: stretch;
            
        }

        /* Agar teks tidak melampaui kotak */
        .kategori-card .card-title {
            word-wrap: break-word; /* Membungkus teks panjang */
            word-break: break-word; /* Memastikan teks tetap dalam batas */
            overflow: hidden; /* Menghindari overflow */
            display: -webkit-box; /* Untuk multiline truncate jika diperlukan */
            -webkit-line-clamp: 3; /* Maksimal 3 baris teks */
            -webkit-box-orient: vertical;
        }

        /* Ikon dan teks tetap seimbang */
        .kategori-card .d-flex {
            height: 100%;
        }

        .pie-container{
            position: relative;
            height: 420px !important;
            width: 420px !important;
        }

        
    </style>
@endsection

@section('pages')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row bg-white p-3 mb-3">
            <div>
                <p class="fs-4 fw-bold mb-0">Dashboard LaporMasWapres!</p>
                <p class="mb-0">Halo, {{auth('admin')->user()->username}} </p>
            </div>
        </div>
        <div class="d-block mb-3">
            <div class="row g-3">
                <!-- Total Laporan -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-newspaper fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $totalLaporan }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Total Data Laporan</small>
                        </div>
                    </div>
                </div>

                <!-- Card Gabungan Laki-Laki dan Perempuan -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-users fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title mb-0">Laki-Laki: {{ $lakiLaki }}</p>
                                <p class="card-title mb-0">Perempuan: {{ $perempuan }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Total Pengadu Berdasarkan Gender</small>
                        </div>
                    </div>
                </div>

                <!-- Cards Deputi 1-4 untuk Admin -->
                @if(auth('admin')->user()->role === 'admin')
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-briefcase fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $deputi1 }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Deputi 1</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-briefcase fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $deputi2 }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Deputi 2</small>
                        </div>
                    </div>
                </div>

                <!-- Jumlah Aduan yang Terdisposisi -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-check-circle fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $totalTerdisposisi }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Aduan Terdisposisi</small>
                        </div>
                    </div>
                </div>
                <!-- Jumlah Aduan yang Belum Terdisposisi -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fa fa-exclamation fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $belumTerdisposisi }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Belum Terdisposisi</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-briefcase fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $deputi3 }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Deputi 3</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-briefcase fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $deputi4 }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Deputi 4</small>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-6">
                    <div class="card border-0 shadow-sm p-3 h-100 justify-content-center">
                        <canvas id="laporanHarianChart"></canvas>
                    </div>
                </div>

                <!-- Pie Chart Status untuk Deputi -->
                <div class="col-6">
                    <div class="card border-0 shadow-sm p-3 align-items-center">
                        <div class="pie-container">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm p-3">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                </div>
                <div class="col-6">
                    <!-- Chart Provinsi -->
                    <div class="card border-0 shadow-sm p-3 mb-3">
                        <canvas id="provinsiChart"></canvas>
                    </div>
                </div>
                <div class="col-6">
                    <!-- Judul paling sering disebut -->
                    <div class="card border-0 shadow-sm p-3 mb-3">
                        <canvas id="judulChart"></canvas>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm p-3">
                        <p class="fs-4 fw-bold mb-0">Jumlah Laporan Berdasarkan Kategori</p>
                        <div class="row">
                            @foreach ($laporanPerKategori as $kategori)
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <a href="{{ route('admin.laporan', ['filterKategori' => $kategori->kategori]) }}" class="text-decoration-none">
                                        <div class="card kategori-card border-0 shadow-sm p-2 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <p class="card-title fs-5 mb-2 text-wrap">{{ $kategori->kategori }}</p>
                                                    <p class="text-muted mb-0">{{ $kategori->total }} laporan</p>
                                                </div>
                                                <i class="fas fa-folder fa-3x text-primary"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('laporanHarianChart').getContext('2d');
        const laporanHarian = @json($laporanHarian);

        const labels = laporanHarian.map(item => item.tanggal);
        const data = laporanHarian.map(item => item.total);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Laporan Harian',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: { enabled: true },
                    datalabels: { // Menampilkan angka di atas batang
                        anchor: 'end',
                        align: 'start',
                        formatter: (value) => value,
                        color: '#000',
                        font: { weight: 'bold' }
                    }
                },
                scales: {
                    x: { title: { display: true, text: 'Tanggal' } },
                    y: { title: { display: true, text: 'Jumlah Laporan' }, beginAtZero: true }
                }
            },
            plugins: [ChartDataLabels] // Tambahkan plugin untuk menampilkan data
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kategoriData = @json($laporanPerKategori);
        const labelsKategori = kategoriData.map(item => item.kategori);
        const dataKategori = kategoriData.map(item => item.total);

        new Chart(document.getElementById('kategoriChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsKategori,
                datasets: [{
                    label: 'Jumlah Laporan per Kategori',
                    data: dataKategori,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Kategori' } },
                    y: { title: { display: true, text: 'Jumlah Laporan' }, beginAtZero: true }
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data untuk Chart Provinsi
        const provinsiData = @json($provinsiData);

        const labelsProvinsi = provinsiData.map(item => item.provinsi); // Nama provinsi
        const dataProvinsi = provinsiData.map(item => item.total); // Jumlah laporan

        new Chart(document.getElementById('provinsiChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsProvinsi,
                datasets: [{
                    label: 'Jumlah Laporan per Provinsi',
                    data: dataProvinsi,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Provinsi' } },
                    y: { title: { display: true, text: 'Jumlah Laporan' }, beginAtZero: true }
                }
            }
        });

        // Data untuk Chart Judul
        const judulData = @json($judulFrequencies);

        const labelsJudul = judulData.map(item => item.judul); // Judul pengaduan
        const dataJudul = judulData.map(item => item.total); // Frekuensi judul

        new Chart(document.getElementById('judulChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsJudul,
                datasets: [{
                    label: 'Judul Pengaduan Paling Sering',
                    data: dataJudul,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Judul Pengaduan' } },
                    y: { title: { display: true, text: 'Jumlah Pengaduan' }, beginAtZero: true }
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('statusPieChart').getContext('2d');

        // Data dari controller
        const chartData = @json($chartData);

        // Pisahkan label dan nilai
        const labels = chartData.map(data => data.label); // Ambil label yang sudah diformat
        const values = chartData.map(data => data.value); // Ambil nilai

        new Chart(ctx, {
            type: 'pie', // Jenis chart
            data: {
                labels: labels, // Label status singkat + jumlah
                datasets: [{
                    label: 'Status Laporan',
                    data: values, // Data jumlah laporan
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)', // Warna untuk status 1
                        'rgba(54, 162, 235, 0.5)', // Warna untuk status 2
                        'rgba(255, 206, 86, 0.5)', // Warna untuk status 3
                        'rgba(75, 192, 192, 0.5)'  // Warna untuk status 4
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    });
</script>
@endsection