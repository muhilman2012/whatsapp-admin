<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Laporan Harian Lapor Mas Wapres!</h1>
    @if ($startDate === $endDate)
        <p>Tanggal: {{ $startDate }}</p>
    @else
        <p>Dari: {{ $startDate }} Sampai: {{ $endDate }}</p>
    @endif
    <table>
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Nomor Tiket</th>
                <th>Nama Pengadu</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Disposisi</th>
                <th>Nama Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $index => $report)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $report->nomor_tiket }}</td>
                <td>{{ $report->nama_lengkap }}</td>
                <td>{{ $report->judul }}</td>
                <td>{{ $report->kategori }}</td>
                <td>{{ $report->disposisi_terbaru ?? $report->disposisi }}</td>
                <td>{{ $report->petugas }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>