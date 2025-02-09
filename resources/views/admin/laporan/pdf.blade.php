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
        <p>
            <strong>Identitas Pengadu:</strong> {{ $laporan->nama_lengkap }} / {{ $laporan->nik }} / 
            @if(!empty($laporan->nomor_pengadu))
                {{ $laporan->nomor_pengadu }}
            @else
                <span class="text-danger">Tidak ada Nomor HP</span>
            @endif / 
            @if(!empty($laporan->email))
                {{ $laporan->email }}
            @else
                <span class="text-danger">Tidak ada <i>email</i></span>
            @endif
        </p>
        <p><strong>Judul Aduan:</strong> {{ $laporan->judul }}</p>
        <p><strong>Isi Laporan:</strong></p>
        <p>{{ $laporan->detail }}</p>
    </div>

    <p><strong>Catatan:</strong></p>
    <ol>
        <li>Bukti pengaduan ini digunakan hanya untuk kepentingan pelayanan pengaduan masyarakat dan tidak dapat digunakan untuk kepentingan peradilan hukum lainnya.</li>
        <li>Pengadu dapat melacak status pengaduan melalui nomor <i>whatsapp</i> 081117042204 tanpa perlu hadir langsung ke kantor Sekretariat Wakil Presiden.</li>
        <li>Jika diminta untuk melengkapi kekurangan dokumen pendukung dapat dikirim melalui nomor <i>whatsapp</i> 081117042204.</li>
    </ol>

    <div class="watermark">
        <img src="{{ public_path('images/logo/LaporMasWapres.png') }}" alt="Logo" style="width: 500px;">
    </div>
</body>
</html>