@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Detail Pengaduan</title>
@endsection

@section('pages')
<div class="container-fluid">
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom">
            <p class="fs-4 fw-bold mb-0">Detail Pengaduan</p>
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
                            @endif

                            @if($data->dokumen_tambahan)
                                <a href="{{ $data->dokumen_tambahan }}" target="_blank"><span class="badge bg-warning">Lihat Kekurangan Dokumen</span></a>
                            @endif  
                        </div>  
                        @elseif($data->sumber_pengaduan === 'tatap muka')  
                            <!-- Jika sumber pengaduan adalah Tatap Muka -->  
                            <div>  
                                <!-- Cek apakah ada dokumen yang disimpan di field dokumen_pendukung -->
                                @if (!empty($data->dokumen_pendukung))
                                    <!-- Cek jika dokumen_pendukung adalah URL atau path lokal -->
                                    @if (filter_var($data->dokumen_pendukung, FILTER_VALIDATE_URL))
                                        <!-- Jika dokumen_pendukung adalah URL, tampilkan sebagai link -->
                                        <a href="{{ $data->dokumen_pendukung }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen Pengaduan</span></a>
                                    @else
                                        <!-- Jika dokumen_pendukung adalah file lokal, tampilkan menggunakan asset -->
                                        <a href="{{ asset('storage/dokumen/' . $data->dokumen_pendukung) }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen Pengaduan</span></a>
                                    @endif
                                @else
                                    <!-- Jika tidak ada, tampilkan semua dokumen dari tabel dokumens -->
                                    @forelse ($data->dokumens as $dokumen)
                                        <a href="{{ asset('storage/dokumen/' . $dokumen->file_name) }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen</span></a>
                                    @empty
                                        <span class="badge bg-primary">Tidak ada Dokumen Pengaduan</span>
                                    @endforelse
                                @endif

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
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Status Laporan:</p>
                    <span class="badge bg-primary">{{ $data->status }}</span>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Tanggapan:</p>
                    <p>{{ $data->tanggapan ?? 'Belum ada tanggapan' }}</p>
                </div>
            </div>
            <div class="mb-3 mt-3 text-end">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editPengaduanModal">
                    Ubah Data
                </button>
                <button class="btn btn-secondary" onclick="window.history.back()">Kembali ke Halaman Sebelumnya</button>
            </div>
        </div>
    </div>
    <div class="mb-3 mt-3 text-end">
        <a href="{{ route('admin.laporan.tandaterima', $data->nomor_tiket) }}" class="btn btn-primary">
            Unduh Tanda Terima Pengaduan (untuk Pengadu)
        </a>
        <a href="{{ route('admin.laporan.download', $data->nomor_tiket) }}" class="btn btn-success">
            Unduh Tanda Terima (untuk TL K/L/D)
        </a>
    </div>
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom">
            <p class="fs-4 fw-bold mb-0">Aktivitas terkait</p>
        </div>
        <div class="d-block p-3">
            @if ($logs->isEmpty())
                <p>Tidak ada log aktivitas.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Aktivitas</th>
                                <th>Pengguna</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                    <td>{{ $log->activity }}</td>
                                    <td>{{ $log->user->nama }}</td> <!-- Asumsi setiap log memiliki relasi 'user' yang terisi -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <!-- Modal Edit Pengaduan -->
    <div class="modal fade" id="editPengaduanModal" tabindex="-1" aria-labelledby="editPengaduanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPengaduanModalLabel">Ubah Data Pengaduan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.laporan.ubah', $data->nomor_tiket) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ $data->nama_lengkap }}">
                            </div>
                            <div class="col-md-6">
                                <label for="nik" class="form-label fw-bold">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" value="{{ $data->nik }}">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $data->email ?? 'Tidak diisi' }}">
                            </div>
                            <div class="col-md-6">
                                <label for="nomor_pengadu" class="form-label fw-bold">Nomor Pengadu</label>
                                <input type="text" class="form-control" id="nomor_pengadu" name="nomor_pengadu" value="{{ $data->nomor_pengadu }}">
                            </div>
                            <div class="col-md-12">
                                <label for="alamat_lengkap" class="form-label fw-bold">Alamat Lengkap</label>
                                <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="2">{{ $data->alamat_lengkap }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="judul" class="form-label fw-bold">Judul Laporan</label>
                                <input type="text" class="form-control" id="judul" name="judul" value="{{ $data->judul }}">
                            </div>
                            <div class="col-md-12">
                                <label for="detail" class="form-label fw-bold">Detail Laporan</label>
                                <textarea class="form-control" id="detail" name="detail" rows="5">{{ $data->detail }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="dokumen_pendukung" class="form-label fw-bold">Dokumen Pendukung</label>
                                <input type="file" class="form-control" id="dokumen_pendukung" name="dokumen_pendukung[]" multiple accept=".pdf, .docx, .jpg, .jpeg, .png">
                                <small class="text-muted">Maximal 4MB per file. Hanya PDF, DOCX, JPG, PNG, JPEG.</small>
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
    </div>
</div>
@endsection