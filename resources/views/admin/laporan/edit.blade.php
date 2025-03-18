@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Edit Data Pengaduan</title>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <p>{{ $data->nama_lengkap }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Status:</p>
                    <span class="badge bg-primary">{{ $data->status }}</span>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Tanggapan:</p>
                    <p>{{ $data->tanggapan ?? 'Belum ada tanggapan' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Kategori:</p>
                    <p>{{ $data->kategori ?? 'Belum ada kategori' }}</p>
                </div>
                <div class="col-md-6">  
                    <p class="text-label fw-bold mb-1">Disposisi:</p>  
                    <p>  
                        {{ isset($namaDeputi[$data->disposisi]) ? $namaDeputi[$data->disposisi] : 'Belum ada disposisi' }}  
                    </p>  
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
                    @if($data->sumber_pengaduan === 'whatsapp')
                        <!-- Jika sumber pengaduan adalah WhatsApp -->
                        <div>
                            @if($data->dokumen_ktp)
                                <a href="{{ $data->dokumen_ktp }}" target="_blank"><span class="badge bg-primary">Lihat Identitas</span></a>
                            @endif

                            @if($data->dokumen_kk)
                                <a href="{{ $data->dokumen_kk }}" target="_blank"><span class="badge bg-primary">Lihat KK</span></a>
                            @endif

                            @if($data->dokumen_skuasa)
                                <a href="{{ $data->dokumen_skuasa }}" target="_blank"><span class="badge bg-primary">Lihat Surat Kuasa</span></a>
                            @endif

                            @if($data->dokumen_pendukung)
                                <a href="{{ $data->dokumen_pendukung }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen Pengaduan</span></a>
                            @else
                                <span class="badge bg-primary">Tidak ada Dokumen Pengaduan</span>
                            @endif

                            <!-- Tampilkan semua dokumen terkait dari tabel dokumens -->
                            @foreach ($data->dokumens as $dokumen)
                                <a href="{{ asset('storage/dokumen/' . $dokumen->file_name) }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen</span></a>
                            @endforeach

                            <!-- Menampilkan dokumen tambahan jika ada -->
                            @if($data->dokumen_tambahan)
                                <a href="{{ $data->dokumen_tambahan }}" target="_blank"><span class="badge bg-warning">Lihat Kekurangan Dokumen</span></a>
                            @endif
                        </div>
                    @elseif($data->sumber_pengaduan === 'tatap muka')
                        <!-- Jika sumber pengaduan adalah Tatap Muka -->
                        <div>
                            @if($data->dokumen_pendukung)
                                <a href="{{ asset('storage/dokumen/' . $data->dokumen_pendukung) }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen Pengaduan</span></a>
                            @else
                                <span class="badge bg-primary">Tidak ada Dokumen Pengaduan</span>
                            @endif
                            <!-- Tampilkan semua dokumen terkait dari tabel dokumens -->
                            @foreach ($data->dokumens as $dokumen)
                                <a href="{{ asset('storage/dokumen/' . $dokumen->file_name) }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen</span></a>
                            @endforeach

                            <!-- Menampilkan dokumen tambahan jika ada -->
                            @if($data->dokumen_tambahan)
                                <a href="{{ $data->dokumen_tambahan }}" target="_blank"><span class="badge bg-warning">Lihat Kekurangan Dokumen</span></a>
                            @endif
                        </div>
                    @else
                        <!-- Jika sumber pengaduan tidak diketahui -->
                        <p>Sumber pengaduan tidak valid</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->role === 'analis')
    <div class="d-block rounded bg-white shadow mb-3 p-5">
        <form action="{{ route('admin.laporan.analis.store', $data->nomor_tiket) }}" method="post">
            @csrf
            <div class="row">
                <!-- Status Analisis -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Status Analisis</label>
                    <span class="badge bg-primary">{{ $data->status_analisis }}</span>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Catatan Analisis</label>
                    <p>{{ $data->catatan_analisis }}</p>
                </div>
            </div>
            <!-- Input Lembar Kerja Analis -->
            <div class="mb-3">
                <label for="lembar_kerja_analis" class="form-label fw-bold">Lembar Kerja Analis</label>
                <textarea name="lembar_kerja_analis" id="lembar_kerja_analis" rows="6" class="form-control">{{ old('lembar_kerja_analis', $data->lembar_kerja_analis) }}</textarea>
            </div>
            <!-- Tombol Submit -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">Kirim Analisis</button>
            </div>
        </form>
    </div>
    @endif
    
    <!-- Bagian Form Edit -->
    <div class="d-block rounded bg-white shadow p-5">
        <form action="{{ route('admin.laporan.update', $data->nomor_tiket) }}" method="post" id="formEditLaporan">
            @csrf
            @method('put')
            <!-- Dropdown Status -->
            <div class="mb-3">
                <label for="status" class="form-label fw-bold">Status</label>
                <select name="status" id="status" class="form-control select2">
                    <option value="" selected>Pilih Status</option>
                    <option value="Penanganan Selesai" {{ $data->status === 'Penanganan Selesai' ? 'selected' : 'Penanganan Selesai' }}>Penanganan Selesai</option>
                    <option value="Menunggu kelengkapan data dukung dari Pelapor" {{ $data->status === 'Menunggu kelengkapan data dukung dari Pelapor' ? 'selected' : '' }}>Menunggu kelengkapan data dukung dari Pelapor</option>
                    <option value="Diteruskan kepada instansi yang berwenang untuk penanganan lebih lanjut" {{ $data->status === 'Diteruskan kepada instansi yang berwenang untuk penanganan lebih lanjut' ? 'selected' : '' }}>Diteruskan kepada instansi yang berwenang untuk penanganan lebih lanjut</option>
                    <option value="Proses verifikasi dan telaah" {{ $data->status === 'Proses verifikasi dan telaah' ? 'selected' : '' }}>Proses verifikasi dan telaah</option>
                </select>
            </div>

            <!-- Input Tanggapan -->
            <div class="mb-3">
                <label for="tanggapan" class="form-label fw-bold">Tanggapan <small>(Tanggapan ini dapat dilihat oleh Pengadu)</small></label>
                <textarea name="tanggapan" id="tanggapan" rows="6" class="form-control">{{ old('tanggapan', $data->tanggapan) }}</textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center my-3">
                <button class="btn btn-secondary" onclick="window.history.back()">Kembali</button>
                @if (in_array(auth('admin')->user()->role, ['superadmin' ,'admin']))
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#instansiTujuanModal">Teruskan ke Instansi</button>
                @endif
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#perbaruiModal">Perbarui Pengaduan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Perbarui Pengaduan -->
<div class="modal fade" id="perbaruiModal" tabindex="-1" aria-labelledby="perbaruiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Perbarui Pengaduan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin memperbarui pengaduan ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" form="formEditLaporan">Ya, Perbarui</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Instansi -->
<div class="modal fade" id="instansiTujuanModal" tabindex="-1" aria-labelledby="instansiTujuanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Teruskan ke Instansi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.laporan.teruskanKeInstansi', $data->nomor_tiket) }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="institution" class="form-label fw-bold">Pilih Instansi Tujuan</label>
                        <select name="institution" id="institution" class="form-control select2" style="width: 100%;">
                            <option value="">Pilih Instansi</option>
                            @foreach ($institutions as $institution)
                                <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label fw-bold">Keterangan untuk Instansi Tujuan</label>
                        <textarea name="reason" id="reason" rows="6" class="form-control">{{ old('reason', $data->reason) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Kirim ke Instansi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
@endsection
