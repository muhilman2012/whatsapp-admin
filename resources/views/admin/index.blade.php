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

                <!-- Pengadu Laki-Laki -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-male fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $lakiLaki }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Pengadu Laki-laki</small>
                        </div>
                    </div>
                </div>

                <!-- Pengadu Perempuan -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-female fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $perempuan }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Pengadu Perempuan</small>
                        </div>
                    </div>
                </div>
                <!-- Pengadu ??? -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-question fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">0</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Dashboard Tambahan</small>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm p-3">
                        <canvas id="laporanHarianChart"></canvas>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('laporanHarianChart').getContext('2d');
        const laporanHarian = @json($laporanHarian); // Data dari controller

        // Parsing data untuk chart
        const labels = laporanHarian.map(item => item.tanggal); // Tanggal
        const data = laporanHarian.map(item => item.total); // Total laporan per hari

        new Chart(ctx, {
            type: 'line', // Jenis chart
            data: {
                labels: labels,
                datasets: [{
                    label: 'Laporan Harian',
                    data: data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.4, // Kurva chart
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                },
                scales: {
                    x: { title: { display: true, text: 'Tanggal' } },
                    y: { title: { display: true, text: 'Jumlah Laporan' }, beginAtZero: true },
                }
            }
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
@endsection