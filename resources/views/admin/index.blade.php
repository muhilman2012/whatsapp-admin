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
                            <i class="fas fa-newspaper fa-3x me-2"></i>
                            <div class="card-body p-0 text-end">
                                <p class="mb-0" style="font-size: 1.1rem;">WA: {{ $whatsapp }}</p>
                                <p class="mb-0" style="font-size: 1.1rem;">TM: {{ $tatapMuka }}</p>
                                <p class="mb-0" style="font-size: 1.1rem;">Surat: {{ $suratFisik }}</p>
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

                @if (in_array(auth('admin')->user()->role, ['superadmin', 'admin']))
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
                            <small class="text-start fw-bold">Aduan Terdistribusi</small>
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
                            <small class="text-start fw-bold">Belum Terdistribusi</small>
                        </div>
                    </div>
                </div>
                @endif

                @if (in_array(auth('admin')->user()->role, ['superadmin', 'admin']))
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-newspaper fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title mb-0">WA: {{ $deputi_1WhatsApp }}</p>
                                <p class="card-title mb-0">TM: {{ $deputi_1TatapMuka }}</p>
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
                            <i class="fas fa-newspaper fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title mb-0">WA: {{ $deputi_2WhatsApp }}</p>
                                <p class="card-title mb-0">TM: {{ $deputi_2TatapMuka }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Deputi 2</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-newspaper fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title mb-0">WA: {{ $deputi_3WhatsApp }}</p>
                                <p class="card-title mb-0">TM: {{ $deputi_3TatapMuka }}</p>
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
                            <i class="fas fa-newspaper fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title mb-0">WA: {{ $deputi_4WhatsApp }}</p>
                                <p class="card-title mb-0">TM: {{ $deputi_4TatapMuka }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Deputi 4</small>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart Laporan Harian -->
                <div class="col-lg-6 col-sm-12">
                    <div class="card border-0 shadow-sm p-3 h-100 justify-content-center">
                        <canvas id="laporanHarianChart"></canvas>
                    </div>
                </div>

                <!-- Pie Chart Status untuk Deputi -->
                <div class="col-lg-6 col-sm-12">
                    <div class="card border-0 shadow-sm p-3 align-items-center">
                        <div class="pie-container">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Pie Chart Per Deputi -->
                <div class="col-12">
                    <div class="row">
                        @foreach (['deputi_1', 'deputi_2', 'deputi_3', 'deputi_4'] as $deputi)
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="card border-0 shadow-sm p-3 align-items-center">
                                    <h6 class="fw-bold text-center">Status Laporan {{ ucfirst(str_replace('_', ' ', $deputi)) }}</h6>
                                    <div class="pie-container">
                                        <canvas id="statusPieChart{{ $deputi }}"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-12 col-lg-12 col-sm-12">
                    <div class="card border-0 shadow-sm p-3">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                </div>
                @endif

                @if (in_array(auth('admin')->user()->role, ['deputi_1', 'deputi_2', 'deputi_3', 'deputi_4', 'asdep']))
                <!-- Jumlah Aduan yang Terdisposisi ke Analis -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fas fa-user-check fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $totalAssignedToAnalis }}</p> <!-- Menampilkan jumlah terdisposisi ke analis -->
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Terdisposisi ke Analis</small>
                        </div>
                    </div>
                </div>
                <!-- Jumlah Aduan yang Belum Terdisposisi -->
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card border-0 p-2 shadow-sm">
                        <div class="d-flex align-items-center px-2">
                            <i class="fa fa-exclamation fa-3x"></i>
                            <div class="card-body text-end">
                                <p class="card-title fs-2 mb-0">{{ $totalNotAssigned }}</p> <!-- Menampilkan jumlah belum terdisposisi -->
                            </div>
                        </div>
                        <div class="card-footer bg-white px-1">
                            <small class="text-start fw-bold">Belum Terdisposisi</small>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart Laporan Harian -->
                <div class="col-6">
                    <div class="card border-0 shadow-sm p-3 h-100 justify-content-center">
                        <canvas id="laporanHarianChart"></canvas>
                    </div>
                </div>

                <!-- Pie Chart Status untuk Deputi -->
                <div class="col-6">
                    <div class="card border-0 shadow-sm p-3 align-items-center">
                        <div class="pie-container">
                            <canvas id="statusPieChart{{ auth('admin')->user()->role }}"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm p-3">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                </div>
                @endif

                @if (in_array(auth('admin')->user()->role, ['superadmin', 'admin']))
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
                @endif

                @if (in_array(auth('admin')->user()->role, ['superadmin', 'admin', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4', 'asdep']))
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
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Pengumuman -->   
    <div class="modal fade" id="pengumumanModal" tabindex="-1" aria-labelledby="pengumumanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pengumumanModalLabel">📢 Pengumuman Penting</h5>
                </div>
                <div class="modal-body" style="font-size: 0.9rem;">
                    <p>Dalam rangka peningkatan tata kelola penanganan pengaduan yang lebih tertib dan akuntabel, disampaikan hal-hal berikut:</p>
                    
                    <p><strong>Bagi Analis / JF:</strong></p>
                    <ul>
                        <li>Dimohon untuk menyelesaikan tahapan analisis secara menyeluruh terhadap setiap pengaduan yang diterima.</li>
                        <li>Hasil analisis perlu dilengkapi hingga tahap <em>Lembar Kerja Analis</em>.</li>
                        <li>Pengaduan baru dapat dilanjutkan setelah ada persetujuan dari Asdep/Karo terkait.</li>
                    </ul>

                    <p><strong>Bagi Asdep / Karo:</strong></p>
                    <ul>
                        <li>Dimohon untuk memeriksa dan memberikan persetujuan dari setiap hasil analisis yang diajukan oleh Analis.</li>
                        <li>Status dan tanggapan pengaduan baru dapat diperbarui setelah hasil analisis disetujui.</li>
                    </ul>

                    <hr>
                    <p><strong>📘 Keterangan Status Analisis:</strong></p>
                    <ul>
                        <li><strong>Pending:</strong> Status awal ketika pengaduan baru diteruskan ke Analis/JF.</li>
                        <li><strong>Menunggu Persetujuan:</strong> Analis telah memilih klasifikasi dan mengirimkan hasil analisis.</li>
                        <li><strong>Disetujui:</strong> Asdep/Karo telah menyetujui hasil analisis.</li>
                        <li><strong>Perbaikan:</strong> Asdep/Karo meminta perbaikan atas hasil analisis. Catatan koreksi tersedia dalam form analisis.</li>
                    </ul>

                    <p>🔔 Informasi dan pembaruan terkait proses analisis kini tersedia secara langsung melalui fitur <strong>Notifikasi</strong> pada sistem.</p>

                    <p>Atas perhatian dan kerja sama Bapak/Ibu dalam menjaga kelancaran serta akuntabilitas penanganan pengaduan, kami sampaikan terima kasih.</p>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<!-- Script untuk menampilkan modal 1x per login -->
<!-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Cek apakah modal sudah pernah ditampilkan di sesi ini
        if (!sessionStorage.getItem('pengumumanShown')) {
            const pengumumanModal = new bootstrap.Modal(document.getElementById('pengumumanModal'));
            pengumumanModal.show();

            // Simpan flag ke sessionStorage
            sessionStorage.setItem('pengumumanShown', 'true');
        }
    });
</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('laporanHarianChart').getContext('2d');
        const laporanHarian = @json($laporanHarian);

        // Ambil 20 tanggal terbaru dari data laporan
        const dataTerbaru = laporanHarian.slice(-20); // Ambil 20 elemen terakhir

        // Data untuk chart
        const labels = dataTerbaru.map(item => item.tanggal); // Hanya 20 tanggal terbaru
        const dataWhatsapp = dataTerbaru.map(item => item.total_whatsapp); // Data WA
        const dataTatapMuka = dataTerbaru.map(item => item.total_tatap_muka); // Data TM

        // Inisialisasi Chart.js
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'WhatsApp',
                        data: dataWhatsapp,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)', // Warna untuk WhatsApp
                        barThickness: 30, // Ketebalan bar tetap
                    },
                    {
                        label: 'Tatap Muka',
                        data: dataTatapMuka,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)', // Warna untuk Tatap Muka
                        barThickness: 30, // Ketebalan bar tetap
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Hindari aspek rasio bawaan
                scales: {
                    x: {
                        title: { display: true, text: 'Tanggal' },
                        stacked: true, // Bar bertumpuk
                        ticks: {
                            maxRotation: 45, // Rotasi label agar rapi
                            minRotation: 0,
                        },
                    },
                    y: {
                        title: { display: true, text: 'Jumlah Laporan' },
                        stacked: true, // Bar bertumpuk
                        beginAtZero: true,
                    },
                },
                plugins: {
                    legend: { display: true },
                    tooltip: { enabled: true },
                    datalabels: {
                        anchor: 'end',
                        align: 'start',
                        formatter: (value) => value,
                        color: '#000',
                        font: { weight: 'bold' },
                    },
                },
            },
            plugins: [ChartDataLabels], // Tambahkan plugin untuk menampilkan data
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
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
        // Pastikan data dari controller tersedia
        const chartDataAll = @json($chartData);
        const chartDataDeputi = @json($chartDataDeputi);

        function createPieChart(canvasId, data) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) {
                console.warn(`Canvas dengan ID "${canvasId}" tidak ditemukan.`);
                return;
            }

            if (!data || data.length === 0) {
                console.warn(`Data kosong untuk "${canvasId}".`);
                return;
            }

            const ctx = canvas.getContext('2d');
            const labels = data.map(item => item.label);
            const values = data.map(item => item.value);

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Status Laporan',
                        data: values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)'
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
                            callbacks: {
                                label: function(tooltipItem) {
                                    let index = tooltipItem.dataIndex;
                                    let label = data[index].label;
                                    let whatsappCount = data[index].whatsapp ?? 0;
                                    let tatapMukaCount = data[index].tatap_muka ?? 0;

                                    return [
                                        `${label}`,
                                        `📲 WhatsApp: ${whatsappCount}`,
                                        `🤝 Tatap Muka: ${tatapMukaCount}`
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        }

        // **Inisialisasi pie chart untuk Semua Data (Superadmin & Admin)**
        createPieChart('statusPieChart', chartDataAll);

        // **Inisialisasi pie chart untuk masing-masing deputi (Admin & Superadmin)**
        ['deputi_1', 'deputi_2', 'deputi_3', 'deputi_4'].forEach(deputi => {
            createPieChart(`statusPieChart${deputi}`, chartDataDeputi[deputi]);
        });

        // **Inisialisasi pie chart untuk Deputi yang login**
        const userRole = @json(auth('admin')->user()->role);
        if (['deputi_1', 'deputi_2', 'deputi_3', 'deputi_4'].includes(userRole)) {
            createPieChart(`statusPieChart${userRole}`, chartDataDeputi[userRole]);
        }

        // **Inisialisasi pie chart untuk Asdep**
        if (userRole === 'asdep') {
            createPieChart('statusPieChartAsdep', chartDataDeputi['asdep']);
        }
    });
</script>
@endsection