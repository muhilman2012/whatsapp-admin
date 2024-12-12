<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header h1 {
                margin: 0;
                font-size: 18px;
            }
            .header h2, .header h3 {
                margin: 5px 0;
                font-size: 14px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10px; /* Sesuaikan ukuran font jika tabel terlalu besar */
                table-layout: fixed; /* Atur tabel agar menyesuaikan halaman */
                word-wrap: break-word; /* Pecahkan kata panjang agar tetap dalam sel */
            }
            table, th, td {
                border: 1px solid black;
            }
            th {
                background-color: #f2f2f2;
                text-align: center;
            }
            th, td {
                padding: 5px;
                text-align: left;
                word-wrap: break-word;
                overflow-wrap: break-word;
                white-space: normal; /* Pastikan teks tidak keluar dari sel */
            }
            .center {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>REKAP PENGADUAN LAPOR MAS WAPRES</h1>
            <h2>MELALUI WA CHATBOT (0811-1704-2204)</h2>
            <h3>TANGGAL: {{ $tanggal }}</h3>
            <h3>JUMLAH PENGADUAN: {{ $jumlahPengaduan }}</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">Nomor Tiket</th>
                    <th style="width: 15%;">Nama Lengkap</th>
                    <th style="width: 15%;">NIK</th>
                    <th style="width: 10%;">Nomor Pengadu</th>
                    <th style="width: 15%;">Email</th>
                    <th style="width: 5%;">Jenis Kelamin</th>
                    <th style="width: 25%;">Alamat Lengkap</th>
                    <th style="width: 10%;">Tanggal Kejadian</th>
                    <th style="width: 10%;">Lokasi</th>
                    <th style="width: 15%;">Judul</th>
                    <th style="width: 40%;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporans as $index => $laporan)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $laporan->nomor_tiket }}</td>
                        <td>{{ $laporan->nama_lengkap }}</td>
                        <td>'{{ $laporan->nik }}</td>
                        <td>{{ $laporan->nomor_pengadu }}</td>
                        <td>{{ $laporan->email }}</td>
                        <td>{{ $laporan->jenis_kelamin }}</td>
                        <td>{{ $laporan->alamat_lengkap }}</td>
                        <td>{{ \Carbon\Carbon::parse($laporan->tanggal_kejadian)->format('d-m-Y') }}</td>
                        <td>{{ $laporan->lokasi }}</td>
                        <td>{{ $laporan->judul }}</td>
                        <td>{{ $laporan->detail }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
