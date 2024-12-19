@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Detail Data Pengaduan</title>
@endsection

@section('pages')
<div class="container-fluid">
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom">
            <p class="fs-4 fw-bold mb-0">Detail Data Pengaduan</p>
        </div>
        <div class="d-block p-3">
            <div class="mb-3 row">
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Nomor Tiket:</p>
                    <p>{{ $data->nomor_tiket }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Nama Lengkap:</p>
                    <p>{{ $data->nama_lengkap }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">NIK:</p>
                    <p>{{ $data->nik }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Nomor Pengadu:</p>
                    <p>{{ $data->nomor_pengadu }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Email:</p>
                    <p>{{ $data->email ?? 'Tidak diisi' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Tanggal Kejadian:</p>
                    <p>{{ $data->tanggal_kejadian ? $data->tanggal_kejadian->format('d-m-Y') : 'Tidak diisi' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Judul Laporan:</p>
                    <p>{{ $data->judul }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Detail Laporan:</p>
                    <p>{{ $data->detail }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Alamat Lengkap:</p>
                    <p>{{ $data->alamat_lengkap }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Dokumen Pendukung:</p>
                    @if($data->dokumen_pendukung)
                        @if($data->sumber_pengaduan === 'whatsapp')
                            <!-- Jika sumber pengaduan adalah WhatsApp -->
                            <a href="{{ $data->dokumen_pendukung }}" target="_blank">
                                Lihat Dokumen
                            </a>
                        @elseif($data->sumber_pengaduan === 'tatap muka')
                            <!-- Jika sumber pengaduan adalah Tatap Muka -->
                            <a href="{{ asset('storage/dokumen/' . $data->dokumen_pendukung) }}" target="_blank">
                                Lihat Dokumen
                            </a>
                        @else
                            <!-- Jika sumber pengaduan tidak diketahui -->
                            <p>Sumber pengaduan tidak valid</p>
                        @endif
                    @else
                        <p>Tidak ada dokumen pendukung</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Status Laporan:</p>
                    <p>{{ $data->status }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Tanggapan:</p>
                    <p>{{ $data->tanggapan ?? 'Belum ada tanggapan' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Status Analisis:</p>
                    <p>{{ $data->status_analisis }}</p>

                    @if (auth()->user()->hasRole(['admin', 'asdep', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4']))
                        <form action="{{ route('admin.laporan.approval', $data->nomor_tiket) }}" method="post" class="d-inline">
                            @csrf
                            @method('put')
                            <button type="submit" name="approval_action" value="approved" class="btn btn-success">Setujui Analisis</button>
                            <button type="submit" name="approval_action" value="rejected" class="btn btn-danger">Revisi Analisis</button>
                        </form>
                    @endif
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Analisis dari JF:</p>
                    <p>{{ $data->lembar_kerja_analis ?? 'Belum ada analisis' }}</p>
                </div>
            </div>
            <button class="btn btn-secondary mt-3" onclick="window.history.back()">Kembali ke Halaman Sebelumnya</button>
            <a href="{{ route('admin.laporan.edit', $data->nomor_tiket) }}" class="btn btn-primary mt-3">Update Pengaduan</a>
        </div>
    </div>
    <div class="mt-3 text-end">
        <a href="{{ route('admin.laporan.download', $data->nomor_tiket) }}" class="btn btn-success">
            Download Bukti Pengaduan (PDF)
        </a>
    </div>
</div>
@endsection