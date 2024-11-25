@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Edit Data Pengaduan</title>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('pages')
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
                    <p>{{ $data->nama_lengkap }}</p>
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
            </div>
        </div>
    </div>

    <!-- Bagian Form Edit -->
    <div class="d-block rounded bg-white shadow p-5">
        <form action="{{ route('admin.laporan.update', $data->nomor_tiket) }}" method="post">
            @csrf
            @method('put')
            <div class="mb-3">
                <label for="kategori" class="form-label fw-bold">Kategori</label>
                <select name="kategori" id="kategori" class="form-control select2">
                    <option value="" selected>Pilih Kategori</option>
                    @foreach ($kategori as $item)
                        <option value="{{ $item }}" {{ $data->kategori == $item ? 'selected' : '' }}>{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="disposisi" class="form-label fw-bold">Disposisi</label>
                <select name="disposisi" id="disposisi" class="form-control select2">
                    <option value="" selected>Pilih Disposisi</option>
                    @foreach ($disposisi as $item)
                        <option value="{{ $item }}" {{ $data->disposisi == $item ? 'selected' : '' }}>{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label fw-bold">Status</label>
                <select name="status" id="status" class="form-control select2">
                    <option value="" selected>Pilih Status</option>
                    <option value="Proses verifikasi dan telaah" {{ $data->status == 'Proses verifikasi dan telaah' ? 'selected' : '' }}>Proses verifikasi dan telaah</option>
                    <option value="Diteruskan ke instansi terkait" {{ $data->status == 'Diteruskan ke instansi terkait' ? 'selected' : '' }}>Diteruskan ke instansi terkait</option>
                    <option value="Penanganan selesai" {{ $data->status == 'Penanganan selesai' ? 'selected' : '' }}>Penanganan selesai</option>
                    <option value="Tidak dapat diproses lebih lanjut" {{ $data->status == 'Tidak dapat diproses lebih lanjut' ? 'selected' : '' }}>Tidak dapat diproses lebih lanjut</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="tanggapan" class="form-label fw-bold">Tanggapan</label>
                <textarea name="tanggapan" id="tanggapan" rows="3" class="form-control">{{ $data->tanggapan }}</textarea>
            </div>

            <!-- <div class="mb-3">
                <label for="klasifikasi" class="form-label fw-bold">Klasifikasi</label>
                <select name="klasifikasi" id="klasifikasi" class="form-control select2">
                    <option value="" selected>Pilih Klasifikasi</option>
                    @foreach ($klasifikasi as $item)
                        <option value="{{ $item }}" {{ $data->klasifikasi == $item ? 'selected' : '' }}>{{ $item }}</option>
                    @endforeach
                </select>
            </div> -->

            <div class="my-3 mx-auto" style="width:200px;">
                <button type="submit" class="btn btn-primary form-control">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
