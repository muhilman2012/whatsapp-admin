<div>
    <!-- Input Pencarian dengan Debounce -->
    <input type="text" wire:model.debounce.500ms="searchTerm" class="form-control mb-3" placeholder="Cari berdasarkan Nama, NIK, Judul, atau Nomor Tiket...">

    <!-- Indikator Loading -->
    <div wire:loading wire:target="searchTerm">
        <div class="alert alert-info">
            Sedang memuat data...
        </div>
    </div>

    @if(!empty($searchTerm))
        @if($results->isNotEmpty())
            <!-- Tabel Data -->
            <div class="table-responsive" wire:loading.remove wire:target='search'>
                <table class="table table-borderless table-striped table-hover mt-3">
                    <thead class="alert-secondary">
                        <tr>
                            <th scope="col">#</th>
                            <th>Nomor Tiket</th>
                            <th>Nama Lengkap</th>
                            <th>Judul Pengaduan</th>
                            <th>Kategori</th>
                            <th>Distribusi</th>
                            <th>Sumber</th>
                            <th>Status</th>
                            <th>Dikirim</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $index => $result)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>
                                @if($result->dokumen_tambahan)
                                    <span class="badge bg-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Pengadu telah mengirimkan Dokumen Tambahan">{{ $result->nomor_tiket }}</span>
                                @else
                                    <span>{{ $result->nomor_tiket }}</span>
                                @endif
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($result->nama_lengkap, 20) }}</td>
                            <td>{{ \Illuminate\Support\Str::words($result->judul, 20) }}</td>
                            <td>{{ $result->kategori }}</td>
                            <td>{{ $result->disposisi }}</td>
                            <td>
                                @if($result->sumber_pengaduan === 'tatap muka')
                                    <span class="badge bg-primary">TM</span>
                                @elseif($result->sumber_pengaduan === 'whatsapp')
                                    <span class="badge bg-success">WA</span>
                                @else
                                    <span class="badge bg-secondary">{{ $result->sumber_pengaduan }}</span>
                                @endif
                            </td>
                            <td>{{ $result->status }}</td>
                            <td>{{ $result->created_at->format('d/m/Y') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.laporan.detail2', ['nomor_tiket' => $result->nomor_tiket]) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-eye fa-sm fa-fw"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                Tidak ada data yang ditemukan. Masukkan kata kunci lain untuk mencari laporan.
            </div>
        @endif
    @else
        <div class="alert alert-info" role="alert">
            Masukkan kata kunci untuk mencari laporan.
        </div>
    @endif
</div>