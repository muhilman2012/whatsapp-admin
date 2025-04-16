@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Detail Data Pengaduan</title>
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

                            <!-- Tampilkan semua dokumen terkait dari tabel dokumens -->
                            @foreach ($data->dokumens as $dokumen)
                                <a href="{{ asset('storage/dokumen/' . $dokumen->file_name) }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen</span></a>
                            @endforeach
                        </div>  
                        @elseif(in_array($data->sumber_pengaduan, ['tatap muka', 'surat fisik', 'email']))
                            <!-- Jika sumber pengaduan adalah Tatap Muka -->
                            <div>
                                <!-- Selalu tampilkan dokumen_pendukung jika ada, baik itu URL maupun file lokal -->
                                @if (!empty($data->dokumen_pendukung))
                                    <!-- Jika dokumen_pendukung adalah URL, tampilkan sebagai link yang memicu modal -->
                                    @if(filter_var($data->dokumen_pendukung, FILTER_VALIDATE_URL))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#viewDocumentModal">
                                            <span class="badge bg-primary">Lihat Dokumen Pengaduan di Scloud</span>
                                        </a>
                                    @else
                                        <!-- Jika dokumen_pendukung adalah file lokal, tampilkan menggunakan asset -->
                                        <a href="{{ asset('storage/dokumen/' . $data->dokumen_pendukung) }}" target="_blank"><span class="badge bg-primary">Lihat Dokumen Pengaduan</span></a>
                                    @endif
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
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Status Laporan:</p>
                    <span class="badge bg-primary">{{ $data->status }}</span>
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Tanggapan:</p>
                    <p>{{ $data->tanggapan ?? 'Belum ada tanggapan' }}</p>
                </div>
                <div class="col-md-6">  
                    <p class="text-label fw-bold mb-1">Diposisi dari:</p>  
                    @if ($data->assignments && $data->assignments->isNotEmpty())  
                        @foreach ($data->assignments as $assignment)  
                            <p>{{ $assignment->assignedBy->nama ?? 'Tidak diketahui' }}</p>  
                        @endforeach
                    @else  
                        <p>Tidak ada disposisi</p>  
                    @endif  
                </div>  
                <div class="col-md-6">  
                    <p class="text-label fw-bold mb-1">Catatan Disposisi:</p>  
                    @if ($data->assignments && $data->assignments->isNotEmpty())  
                        @foreach ($data->assignments as $assignment)  
                            <p>{{ $assignment->notes ?? 'Tidak ada catatan' }}</p>  
                        @endforeach  
                    @else  
                        <p>Tidak ada catatan disposisi</p>  
                    @endif  
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Status Analisis:</p>
                    <p><span class="badge bg-primary">{{ $data->status_analisis }}</span> {{ $data->catatan_analisis ?? '' }}</p>

                    @if (auth()->user()->hasRole(['admin', 'asdep', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4']))
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#approvalModal">Setujui/Perbaiki Analisis</button>
                    @endif
                </div>
                <div class="col-md-6">
                    <p class="text-label fw-bold mb-1">Analisis dari JF:</p>
                    <p>{{ $data->lembar_kerja_analis ?? 'Belum ada analisis' }}</p>
                </div>
            </div>
            <button class="btn btn-secondary mt-3" onclick="window.history.back()">Kembali ke Halaman Sebelumnya</button>
            @if (auth()->user()->hasRole(['analis']))
                @if($data->status_analisis !== 'Disetujui') 
                    <!-- Jika status analisis bukan Disetujui, tampilkan tombol Analisis -->
                    <a href="{{ route('admin.laporan.edit', $data->nomor_tiket) }}" class="btn btn-primary mt-3">Analisis</a>
                @else
                    <!-- Jika status analisis sudah Disetujui, tampilkan tombol Perbarui Pengaduan -->
                    <a href="{{ route('admin.laporan.edit', $data->nomor_tiket) }}" class="btn btn-primary mt-3">Perbarui Pengaduan</a>
                @endif
            @endif
            @if (auth()->user()->hasRole(['superadmin','admin', 'asdep', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4']))
                <a href="{{ route('admin.laporan.edit', $data->nomor_tiket) }}" class="btn btn-primary mt-3">Perbarui Pengaduan</a>
            @endif
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
</div>

<!-- Modal Catatan untuk Approval atau Revisi -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.laporan.approval', $data->nomor_tiket) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalLabel">Persetujuan Analisis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="catatan">Catatan (Opsional):</label>
                    <textarea class="form-control" name="catatan" id="catatan" rows="4"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="approval_action" value="approved" class="btn btn-success">Setujui</button>
                    <button type="submit" name="approval_action" value="rejected" class="btn btn-danger">Perbaiki</button>
                </div>
            </div>
        </form>
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