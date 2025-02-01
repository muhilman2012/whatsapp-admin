@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Tambah Pengaduan Baru</title>
<style>
    .ck-editor__editable {
        min-height: 200px;
        box-shadow: unset !important;
        border-radius: 0px 0px 4px 4px !important;
    }
    #detail {
        min-height: 200px; /* Memperbesar field detail */
    }
</style>
@endsection

@section('pages')
<div class="container-fluid">
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom">
            <p class="fs-4 fw-bold mb-0">Tambah Pengaduan Baru</p>
        </div>
        <div class="d-block p-3">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('admin.laporan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Nama dan NIK -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_lengkap" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}">
                        @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nik" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}">
                        @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Jenis Kelamin, Email, Nomor -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="jenis_kelamin" class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                            <option disabled selected>Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label fw-bold">Email Pengadu</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="nomor_pengadu" class="form-label fw-bold">Nomor Handphone Pengadu</label>
                        <input type="text" name="nomor_pengadu" id="nomor_pengadu" class="form-control @error('nomor_pengadu') is-invalid @enderror" value="{{ old('nomor_pengadu') }}">
                        @error('nomor_pengadu')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div class="mb-3">
                    <label for="alamat_lengkap" class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control @error('alamat_lengkap') is-invalid @enderror">{{ old('alamat_lengkap') }}</textarea>
                    @error('alamat_lengkap')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Judul -->
                <div class="mb-3">
                    <label for="judul" class="form-label fw-bold">Judul Laporan <span class="text-danger">*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}">
                    @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Lokasi -->
                <div class="mb-3">
                    <label for="lokasi" class="form-label fw-bold">Lokasi Kejadian</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi') }}">
                    @error('lokasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Detail -->
                <div class="mb-3">
                    <label for="detail" class="form-label fw-bold">Detail Laporan <span class="text-danger">*</span></label>
                    <textarea name="detail" id="detail" class="form-control @error('detail') is-invalid @enderror">{{ old('detail') }}</textarea>
                    @error('detail')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tanggal Kejadian dan Dokumen -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_kejadian" class="form-label fw-bold">Tanggal Kejadian</label>
                        <input type="date" name="tanggal_kejadian" id="tanggal_kejadian" class="form-control @error('tanggal_kejadian') is-invalid @enderror" value="{{ old('tanggal_kejadian') }}">
                        @error('tanggal_kejadian')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="kategori" class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror">
                            <option disabled selected>Pilih Kategori</option>
                            @foreach ($kategoriDeputi as $deputi => $kategoris)
                                @if(isset($namaDeputi[$deputi]))
                                    <optgroup label="{{ $namaDeputi[$deputi] }}">
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori }}" {{ old('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        @error('kategori')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="dokumen_pendukung" class="form-label fw-bold">Dokumen Pendukung <span class="text-danger">*</span></label>
                        <input type="file" name="dokumen_pendukung" id="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror">
                        @error('dokumen_pendukung')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 d-block rounded bg-secondary shadow">
                    <p class="text-white p-3">
                        1. Perhatikan Kolom yang wajib diisi. <br>
                        2. Dokumen Pendukung berupa file PDF/Document/JPG/PNG. <br>
                        3. Disposisi Otomatis ketika Kategori sudah dipilih. <br>
                        4. Jangan Lupa Download Bukti Pengaduan di Halaman Detail Pengaduan (otomatis ke halaman Detail Pengaduan setelah Buat Pengaduan).
                    </p>
                </div>
                <!-- Submit -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary form-control">Buat Pengaduan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')

@endsection