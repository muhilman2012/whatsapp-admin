<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima Pengaduan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 150px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            text-transform: uppercase;
        }
        .box {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }
        .watermark {
            position: fixed;
            top: 35%;
            left: 13%;
            opacity: 0.1;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="a5-page">
        <div class="header">
            <img src="{{ public_path('images/logo/LaporMasWapres.png') }}" alt="Logo">
            <h1>Tanda Terima Pengaduan</h1>
            <h3>Nomor Tiket: <strong>{{ $laporan->nomor_tiket }}</strong></h3>
        </div>
            <div class="box">
                <div class="watermark">
                    <img src="{{ public_path('images/logo/LaporMasWapres.png') }}" alt="Logo" style="width: 500px;">
                </div>
                <p>
                    <strong>Identitas Pengadu:</strong> {{ $laporan->nama_lengkap }} /
                    {{ substr($laporan->nik, 0, 6) . 'XXXXXX' . substr($laporan->nik, -4) }} / 
                    @if(!empty($laporan->nomor_pengadu))
                        {{ substr($laporan->nomor_pengadu, 0, 3) . 'XXXXX' . substr($laporan->nomor_pengadu, -4) }}
                    @else
                        <span class="text-danger">Tidak ada Nomor HP</span>
                    @endif
                </p>
                <p><strong>Catatan:</strong></p>
                <ol>
                    <li>Tanda Terima Pengaduan ini digunakan hanya untuk kepentingan pelayanan pengaduan masyarakat dan tidak dapat digunakan untuk kepentingan peradilan hukum lainnya.</li>
                    <li>Pengadu dapat melacak status pengaduan <strong>secara berkala</strong> melalui nomor <i>whatsapp</i> 081117042204 tanpa perlu hadir langsung ke kantor Sekretariat Wakil Presiden.</li>
                    <li>Jika diminta untuk melengkapi kekurangan dokumen pendukung dapat dikirim melalui nomor <i>whatsapp</i> 081117042204.</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>