<table>
    <thead>
        <tr>
            <th>Nomor Tiket</th>
            <th>Tanggal Pengaduan</th>
            <th>Nama Lengkap</th>
            <th>NIK</th>
            <th>Nomor Pengadu</th>
            <th>Email</th>
            <th>Jenis Kelamin</th>
            <th>Alamat Lengkap</th>
            <th>Tanggal Kejadian</th>
            <th>Lokasi</th>
            <th>Judul</th>
            <th>Detail</th>
            <th>Kategori</th>
            <th>Status</th>
            <th>Tanggapan</th>
            <th>Dokumen KTP</th>
            <th>Dokumen KK</th>
            <th>Dokumen Pendukung</th>
            <th>Dokumen Surat Kuasa</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporans as $laporan)
            <tr>
                <td>{{ $laporan->nomor_tiket }}</td>
                <td>{{ $laporan->created_at->format('d-m-Y') }}</td>
                <td>{{ $laporan->nama_lengkap }}</td>
                <td>{{ $laporan->nik }}</td>
                <td>{{ $laporan->nomor_pengadu }}</td>
                <td>{{ $laporan->email }}</td>
                <td>{{ $laporan->jenis_kelamin }}</td>
                <td>{{ $laporan->alamat_lengkap }}</td>
                <td>{{ \Carbon\Carbon::parse($laporan->tanggal_kejadian)->format('d-m-Y') }}</td>
                <td>{{ $laporan->lokasi }}</td>
                <td>{{ $laporan->judul }}</td>
                <td>{{ $laporan->detail }}</td>
                <td>{{ $laporan->kategori }}</td>
                <td>{{ $laporan->status }}</td>
                <td>{{ $laporan->tanggapan }}</td>
                <td>{{ $laporan->dokumen_ktp }}</td>
                <td>{{ $laporan->dokumen_kk }}</td>
                <td>{{ $laporan->dokumen_pendukung }}</td>
                <td>{{ $laporan->dokumen_skuasa }}</td>
            </tr>
        @endforeach

        <!-- Tabel Kosong -->
        @for ($i = 0; $i < 5; $i++)
            <tr>
                <td colspan="15">&nbsp;</td>
            </tr>
        @endfor
    </tbody>
</table>