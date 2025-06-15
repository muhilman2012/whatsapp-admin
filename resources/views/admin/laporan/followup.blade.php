@extends('admin.layouts.panel')

@section('head')
<title>LaporMasWapres! - Detail Tindak Lanjut</title>
@endsection

@section('pages')
<div class="container fluid">
    <div class="d-block rounded bg-white shadow">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h4>Tindak Lanjut untuk Tiket #{{ $nomor_tiket }}</h4>
        </div>
        <div class="d-block p-3">
        @if($laporan)
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1">
                    <div>
                        <strong>{{ $laporan->nama_lengkap ?? 'Anonim' }}</strong>
                        <span class="badge bg-secondary">{{ ucfirst($laporan->sumber_pengaduan) }}</span>
                        @if($laporan->status === 'Belum Diverifikasi')
                            <span class="badge bg-danger">Belum Diverifikasi</span>
                        @elseif($laporan->status === 'Terverifikasi')
                            <span class="badge bg-success">Terverifikasi</span>
                        @endif
                    </div>
                    <small class="text-muted">{{ $laporan->created_at->format('d M Y, H:i') }}</small>
                </div>
                <h5 class="fw-bold text-primary">{{ $laporan->judul }}</h5>
                <p class="mb-0">{{ $laporan->detail }}</p>

                @if($data->sumber_pengaduan === 'whatsapp')  
                    <!-- Jika sumber pengaduan adalah WhatsApp -->  
                    <div>  
                        @if($data->dokumen_ktp)  
                            <a href="{{ $data->dokumen_ktp }}" target="_blank">
                                <span class="badge bg-primary">Lihat Identitas</span>
                            </a>  
                        @endif  

                        @if($data->dokumen_kk)  
                            <a href="{{ $data->dokumen_kk }}" target="_blank">
                                <span class="badge bg-primary">Lihat KK</span>
                            </a>  
                        @endif  

                        @if($data->dokumen_skuasa)  
                            <a href="{{ $data->dokumen_skuasa }}" target="_blank">
                                <span class="badge bg-primary">Lihat Surat Kuasa</span>
                            </a>  
                        @endif  

                        @if($data->dokumen_pendukung)  
                            <a href="{{ $data->dokumen_pendukung }}" target="_blank">
                                <span class="badge bg-primary">Lihat Dokumen Pengaduan</span>
                            </a>
                        @endif

                        @if($data->dokumen_tambahan)
                            <a href="{{ $data->dokumen_tambahan }}" target="_blank">
                                <span class="badge bg-warning">Lihat Kekurangan Dokumen</span>
                            </a>
                        @endif  

                        @foreach ($data->dokumens as $dokumen)
                            <a href="{{ asset('storage/dokumen/' . $dokumen->file_name) }}" target="_blank">
                                <span class="badge bg-primary">Lihat Dokumen</span>
                            </a>
                        @endforeach
                    </div>  

                @elseif(in_array($data->sumber_pengaduan, ['tatap muka', 'surat fisik', 'email']))
                    <!-- Jika sumber pengaduan adalah Tatap Muka, Surat Fisik, atau Email -->
                    <div>
                        @if (!empty($data->dokumen_pendukung))
                            @if(filter_var($data->dokumen_pendukung, FILTER_VALIDATE_URL))
                                <a href="#" data-bs-toggle="modal" data-bs-target="#viewDocumentModal">
                                    <span class="badge bg-primary">Lihat Dokumen Pengaduan di Scloud</span>
                                </a>
                            @else
                                <a href="{{ asset('storage/dokumen/' . $data->dokumen_pendukung) }}" target="_blank">
                                    <span class="badge bg-primary">Lihat Dokumen Pengaduan</span>
                                </a>
                            @endif
                        @endif

                        @foreach ($data->dokumens as $dokumen)
                            <a href="{{ asset('storage/dokumen/' . $dokumen->file_name) }}" target="_blank">
                                <span class="badge bg-primary">Lihat Dokumen</span>
                            </a>
                        @endforeach

                        @if($data->dokumen_tambahan)
                            <a href="{{ $data->dokumen_tambahan }}" target="_blank">
                                <span class="badge bg-warning">Lihat Kekurangan Dokumen</span>
                            </a>
                        @endif
                    </div>

                @else
                    <!-- Jika sumber pengaduan tidak diketahui -->
                    <p class="text-danger">Sumber pengaduan tidak valid</p>
                @endif
            </div>
        @endif
        @forelse($followups as $item)
            <div class="card my-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1">
                        <div>
                            <strong>{{ $item['institution_from_name'] ?? 'Tidak diketahui' }}</strong>
                            @if($item['institution_to_name'])
                                → <strong>{{ $item['institution_to_name'] }}</strong>
                            @endif
                        </div>
                        <small class="text-muted">{{ $item['created_at'] }}</small>
                    </div>

                    @if($item['template_code'] === 'disposition' && isset($item['template_content']))
                        @php
                            $renderedContent = $item['template_content'];
                            $renderedContent = str_replace('{{institution_to}}', $item['institution_to_name'] ?? '-', $renderedContent);
                            $renderedContent = str_replace('{{institution_from}}', $item['institution_from_name'] ?? '-', $renderedContent);
                            $renderedContent = str_replace('{{institution_to_link}}', '#', $renderedContent);
                            $renderedContent = str_replace('{{institution_from_link}}', '#', $renderedContent);
                        @endphp
                        {!! $renderedContent !!}
                    @else
                        {{ $item['content'] }}
                    @endif

                    {{-- Lampiran --}}
                    @if(isset($item['attachments']['0']['path']))
                        <div class="mt-2">
                            <a href="{{ $item['attachments']['0']['path'] }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> {{ $item['attachments']['0']['file_name'] ?? 'Lihat Lampiran' }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">Tidak ada tindak lanjut ditemukan.</div>
        @endforelse
        </div>
    </div>
</div>
@endsection