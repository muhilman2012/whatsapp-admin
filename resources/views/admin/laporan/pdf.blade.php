<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengaduan</title>
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
            width: 100px;
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
            top: 30%;
            left: 20%;
            opacity: 0.1;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo/LaporMasWapres.png') }}" alt="Logo" style="width: 150px;">
        <h1>Bukti Pengaduan</h1>
        <p>Nomor Pengaduan: <strong>{{ $laporan->nomor_tiket }}</strong></p>
    </div>

    <div class="box">
        <p><strong>Nama Pengadu:</strong> {{ $laporan->nama_lengkap }}</p>
        <p><strong>Judul Aduan:</strong> {{ $laporan->judul }}</p>
        <p><strong>Isi Laporan:</strong></p>
        <p>{{ $laporan->detail }}</p>
    </div>

    <p><strong>Catatan:</strong></p>
    <ol>
        <li>Tanda terima ini digunakan hanya untuk kepentingan pelayanan Pengaduan Masyarakat dan tidak dapat digunakan untuk kepentingan peradilan hukum lainnya.</li>
        <li>Pengadu dapat melacak perkembangan penanganan aduan melalui nomor Whatsapp 0811-1704-2204.</li>
        <li>Untuk kekurangan kelengkapan dokumen pendukung dapat dikirimkan ke alamat email: lapormaswapres@set.wapresri.go.id.</li>
    </ol>

    <div class="watermark">
        <img src="{{ public_path('images/logo/LaporMasWapres.png') }}" alt="Logo" style="width: 500px;">
    </div>
</body>
</html>