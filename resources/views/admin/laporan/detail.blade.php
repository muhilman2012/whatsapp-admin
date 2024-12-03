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
                    <p class="text-label fw-bold mb-1">Status:</p>
                    <p>{{ $data->status }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Tanggapan:</p>
                    <p>{{ $data->tanggapan ?? 'Belum ada tanggapan' }}</p>
                </div>
            </div>
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