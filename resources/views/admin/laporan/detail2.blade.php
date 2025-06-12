@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Detail Pengaduan</title>
@endsection

@section('pages')
<div class="container-fluid">
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <p class="fs-4 fw-bold mb-0">Detail Data Pengaduan</p>
            <p class="text-muted" style="font-size: 1rem;">Waktu Pengaduan : {{ $data->created_at->format('d M Y, H:i') }} <span class="badge bg-primary">{{ $data->sumber_pengaduan }}</span></p>
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
                    <p class="text-label fw-bold mb-1">Kategori dan Judul Laporan: <span class="badge bg-primary">{{ $data->kategori }}</span></p>
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
                        <div>  
                            {{-- Dokumen KTP --}}
                            @if($data->dokumen_ktp)
                                <a href="{{ filter_var($data->dokumen_ktp, FILTER_VALIDATE_URL) ? $data->dokumen_ktp : asset('dokumen/' . basename($data->dokumen_ktp)) }}"
                                target="_blank">
                                    <span class="badge bg-primary">Lihat Identitas</span>
                                </a>  
                            @endif

                            {{-- Dokumen KK --}}
                            @if($data->dokumen_kk)
                                <a href="{{ filter_var($data->dokumen_kk, FILTER_VALIDATE_URL) ? $data->dokumen_kk : asset('dokumen/' . basename($data->dokumen_kk)) }}"
                                target="_blank">
                                    <span class="badge bg-primary">Lihat KK</span>
                                </a>  
                            @endif

                            {{-- Dokumen Surat Kuasa --}}
                            @if($data->dokumen_skuasa)
                                <a href="{{ filter_var($data->dokumen_skuasa, FILTER_VALIDATE_URL) ? $data->dokumen_skuasa : asset('dokumen/' . basename($data->dokumen_skuasa)) }}"
                                target="_blank">
                                    <span class="badge bg-primary">Lihat Surat Kuasa</span>
                                </a>  
                            @endif

                            {{-- Dokumen Pendukung --}}
                            @if($data->dokumen_pendukung)
                                <a href="{{ filter_var($data->dokumen_pendukung, FILTER_VALIDATE_URL) ? $data->dokumen_pendukung : asset('dokumen/' . basename($data->dokumen_pendukung)) }}"
                                target="_blank">
                                    <span class="badge bg-primary">Lihat Dokumen Pengaduan</span>
                                </a>
                            @endif

                            {{-- Dokumen Tambahan: Jika URL disimpan di field, tampilkan --}}
                            @if($data->dokumen_tambahan && filter_var($data->dokumen_tambahan, FILTER_VALIDATE_URL))
                                <a href="{{ $data->dokumen_tambahan }}" target="_blank">
                                    <span class="badge bg-warning">Lihat Kekurangan Dokumen</span>
                                </a>
                            @endif

                            {{-- Tambahan: Tampilkan semua dokumen tambahan dari folder public/dokumen --}}
                            @php
                                $dokumenTambahanFiles = [];
                                $folderPath = public_path('dokumen');

                                if (\Illuminate\Support\Facades\File::exists($folderPath)) {
                                    $allFiles = \Illuminate\Support\Facades\File::files($folderPath);
                                    $dokumenTambahanFiles = collect($allFiles)->filter(function ($file) use ($data) {
                                        return \Illuminate\Support\Str::startsWith($file->getFilename(), $data->nomor_tiket . '_tambahan_');
                                    });
                                }
                            @endphp

                            @foreach ($dokumenTambahanFiles as $file)
                                <a href="{{ asset('dokumen/' . $file->getFilename()) }}" target="_blank">
                                    <span class="badge bg-warning">Lihat Kekurangan Dokumen</span>
                                </a>
                            @endforeach
                        </div>  

                    @elseif(in_array($data->sumber_pengaduan, ['tatap muka', 'surat fisik', 'email']))
                    <div>
                        {{-- Tampilkan dokumen_pendukung --}}
                        @if (!empty($data->dokumen_pendukung))
                            @if(filter_var($data->dokumen_pendukung, FILTER_VALIDATE_URL))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#viewDocumentModal">
                                    <span class="badge bg-primary">Lihat Dokumen Pengaduan di Scloud</span>
                                </a>
                            @else
                                <a href="{{ asset('dokumen/' . basename($data->dokumen_pendukung)) }}" target="_blank">
                                    <span class="badge bg-primary">Lihat Dokumen Pengaduan</span>
                                </a>
                            @endif
                        @endif

                        {{-- Tampilkan dokumen dari tabel dokumens --}}
                        @foreach ($data->dokumens as $dokumen)
                            <a href="{{ asset('storage/dokumen/' . $dokumen->file_name) }}" target="_blank">
                                <span class="badge bg-primary">Lihat Dokumen</span>
                            </a>
                        @endforeach

                        {{-- Dokumen Tambahan: Jika URL disimpan di field, tampilkan --}}
                        @if($data->dokumen_tambahan && filter_var($data->dokumen_tambahan, FILTER_VALIDATE_URL))
                            <a href="{{ $data->dokumen_tambahan }}" target="_blank">
                                <span class="badge bg-warning">Lihat Kekurangan Dokumen</span>
                            </a>
                        @endif
                        
                        {{-- Tambahan: Semua Dokumen Tambahan dari folder public/dokumen --}}
                        @php
                            $folderPath = public_path('dokumen');
                            $dokumenTambahanFiles = [];

                            if (\Illuminate\Support\Facades\File::exists($folderPath)) {
                                $allFiles = \Illuminate\Support\Facades\File::files($folderPath);
                                $dokumenTambahanFiles = collect($allFiles)->filter(function ($file) use ($data) {
                                    return \Illuminate\Support\Str::startsWith($file->getFilename(), $data->nomor_tiket . '_tambahan_');
                                });
                            }
                        @endphp

                        @foreach ($dokumenTambahanFiles as $file)
                            <a href="{{ asset('dokumen/' . $file->getFilename()) }}" target="_blank">
                                <span class="badge bg-warning">Lihat Kekurangan Dokumen</span>
                            </a>
                        @endforeach
                    </div>
                    @else
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
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Analis:</p>
                    @if($assignment = $data->assignments->last())
                        <span>
                            {{ $assignment->assignedTo->nama ?? 'Nama analis tidak tersedia' }} <br>
                            {{ $assignment->assignedTo->unit ?? 'Unit tidak tersedia' }} <br>
                            @php
                                $disposisiToShow = $data->disposisi_terbaru ?? $data->disposisi;
                            @endphp
                            {{ $namaDeputi[$disposisiToShow] ?? $disposisiToShow }}
                        </span>
                    @else
                        <span class="badge bg-secondary">Belum ada analis yang ditugaskan</span>
                    @endif
                </div>
            </div>
            <div class="mb-3 mt-3 text-end">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editPengaduanModal">
                    Ubah Data
                </button>
            </div>
        </div>
    </div>
    <div class="mb-3 mt-3 d-flex justify-content-end align-items-center">
        <div class="text-end">
            
            @if($duplicateReports->count())
                <button class="btn btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#laporanGandaModal">
                    Info Laporan Ganda ({{ $duplicateReports->count() }})
                </button>
            @endif
            <a href="{{ route('admin.laporan.tandaterima', $data->nomor_tiket) }}" class="btn btn-primary mb-3">
                Unduh Tanda Terima Pengaduan (untuk Pengadu)
            </a>
            <a href="{{ route('admin.laporan.download', $data->nomor_tiket) }}" class="btn btn-success mb-3">
                Unduh Tanda Terima (untuk TL K/L/D)
            </a>
        </div>
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
                                <input type="email" class="form-control" id="email" name="email" value="{{ $data->email }}">
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
<!-- Modal untuk melihat dokumen di Scloud -->
<div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDocumentModalLabel">Dokumen Pengaduan di Scloud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda dapat mengklik tombol di bawah ini untuk membuka dokumen di tab baru, atau copy link berikut untuk membuka secara manual jika ada kendala.</p>
                <textarea readonly class="form-control mb-2">{{ $data->dokumen_pendukung }}</textarea>
                <a href="{{ $data->dokumen_pendukung }}" target="_blank" class="btn btn-primary">Buka Dokumen</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Info Laporan Ganda -->
<div class="modal fade" id="laporanGandaModal" tabindex="-1" aria-labelledby="laporanGandaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="laporanGandaModalLabel">
                    Daftar Laporan Ganda berdasarkan NIK/Nomor HP/Email pengadu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nomor Tiket</th>
                                <th>Nama</th>
                                <th>Nomor Pengadu</th>
                                <th>Email</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Disposisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($duplicateReports as $laporan)
                            <tr>
                                <td>{{ $laporan->nomor_tiket }}</td>
                                <td>{{ $laporan->nama_lengkap }}</td>
                                <td>{{ $laporan->nomor_pengadu }}</td>
                                <td>{{ $laporan->email }}</td>
                                <td>{{ \Carbon\Carbon::parse($laporan->created_at)->format('d-m-Y') }}</td>
                                <td>{{ $laporan->kategori ?? '-' }}</td>
                                <td>
                                    @if(!empty($laporan->disposisi_terbaru))
                                        {{ $laporan->disposisi_terbaru }}
                                    @else
                                        {{ $laporan->disposisi }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.laporan.detail', $laporan->nomor_tiket) }}" class="btn btn-sm btn-primary">Lihat</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyTextareaBtn = document.querySelector('#copyTextBtn');

        copyTextareaBtn.addEventListener('click', function(event) {
            const copyTextarea = document.querySelector('.link-textarea');
            copyTextarea.select();
            try {
                const successful = document.execCommand('copy');
                const msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
                alert('Link telah disalin!');
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        });
    });
</script>
@endsection