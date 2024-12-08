@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Edit Data Pengaduan</title>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('pages')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="container-fluid">
    <!-- Bagian Data Saat Ini -->
    <div class="d-block rounded bg-white shadow mb-3">
        <div class="p-3 border-bottom">
            <p class="fs-4 fw-bold mb-0">Data Pengaduan Saat Ini</p>
        </div>
        <div class="d-block p-3">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Nomor Tiket:</p>
                    <p>{{ $data->nomor_tiket }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Nama Lengkap:</p>
                    <p>{{ $data->nama_lengkap }}
                        <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#editNamaModal">
                            Edit
                        </button>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Status:</p>
                    <p>{{ $data->status }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Tanggapan:</p>
                    <p>{{ $data->tanggapan ?? 'Belum ada tanggapan' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Kategori:</p>
                    <p>{{ $data->kategori ?? 'Belum ada kategori' }}</p>
                </div>
                <!-- <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Klasifikasi:</p>
                    <p>{{ $data->klasifikasi ?? 'Belum ada klasifikasi' }}</p>
                </div> -->
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Disposisi:</p>
                    <p>{{ $data->disposisi ?? 'Belum ada disposisi' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Judul:</p>
                    <p>{{ $data->judul }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Detail Pengaduan:</p>
                    <p>{{ $data->detail }}</p>
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
            </div>
        </div>
    </div>

    <!-- Bagian Form Edit -->
    <div class="d-block rounded bg-white shadow p-5">
        <form action="{{ route('admin.laporan.update', $data->nomor_tiket) }}" method="post" id="formEditLaporan">
            @csrf
            @method('put')
            <!-- Dropdown Kategori -->
            <div class="mb-3">
                <label for="kategori" class="form-label fw-bold">Kategori</label>
                <select name="kategori" id="kategori" class="form-control">
                    @foreach ($semuaKategori as $item)
                        <option value="{{ $item }}" {{ $data->kategori == $item ? 'selected' : '' }}>{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Dropdown Disposisi -->
            <div class="mb-3">
                <label for="disposisi" class="form-label fw-bold">Disposisi</label>
                <select name="disposisi" id="disposisi" class="form-control">
                    @foreach ($semuaDisposisi as $key => $value)
                        <option value="{{ $key }}" {{ $data->disposisi == $key ? 'selected' : '' }}>
                            {{ $namaDeputi[$key] ?? $value }} <!-- Gunakan nama deputi dari mapping -->
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Dropdown Status -->
            <div class="mb-3">
                <label for="status" class="form-label fw-bold">Status</label>
                <select name="status" id="status" class="form-control select2">
                    <option value="" selected>Pilih Status</option>
                    <option value="Tidak dapat diproses lebih lanjut (selesai)" {{ $data->status === 'Tidak dapat diproses lebih lanjut (selesai)' ? 'selected' : '' }}>Tidak dapat diproses lebih lanjut (selesai)</option>
                    <option value="Dalam pemantauan terhadap penanganan yang sedang dilakukan oleh instansi berwenang (monitoring K/L)" {{ $data->status === 'Dalam pemantauan terhadap penanganan yang sedang dilakukan oleh instansi berwenang (monitoring K/L)' ? 'selected' : '' }}>Dalam pemantauan terhadap penanganan yang sedang dilakukan oleh instansi berwenang (monitoring K/L)</option>
                    <option value="Disampaikan kepada Pimpinan K/L untuk penanganan lebih lanjut (Tindak Lanjut K/L)" {{ $data->status === 'Disampaikan kepada Pimpinan K/L untuk penanganan lebih lanjut (Tindak Lanjut K/L)' ? 'selected' : '' }}>Disampaikan kepada Pimpinan K/L untuk penanganan lebih lanjut (Tindak Lanjut K/L)</option>
                    <option value="Proses verifikasi & penelahaan" {{ $data->status === 'Proses verifikasi & penelahaan' ? 'selected' : '' }}>Proses verifikasi & penelahaan</option>
                </select>
            </div>

            <!-- Input Tanggapan -->
            <div class="mb-3">
                <label for="tanggapan" class="form-label fw-bold">Tanggapan</label>
                <textarea name="tanggapan" id="tanggapan" rows="3" class="form-control">{{ old('tanggapan', $data->tanggapan) }}</textarea>
            </div>

            <!-- Tombol Update -->
            <div class="my-3 mx-auto" style="width:200px;">
                <button type="submit" class="btn btn-primary form-control">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Nama -->
<div class="modal fade" id="editNamaModal" tabindex="-1" aria-labelledby="editNamaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.laporan.updateNama', $data->nomor_tiket) }}" method="post">
                @csrf
                @method('put')
                <div class="modal-header">
                    <h5 class="modal-title" id="editNamaModalLabel">Edit Nama Lengkap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ $data->nama_lengkap }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
