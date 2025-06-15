<div wire:poll.30s>
    <!-- <div wire:loading>
        <div class="alert alert-info">Memuat data...</div>
    </div> -->
    <div class="mb-3">
        <input type="text" wire:model.debounce.500ms="searchTerm" class="form-control" placeholder="Cari berdasarkan Nama, NIK, atau Nomor HP...">
    </div>
    <div wire:loading.delay wire:target="searchTerm" class="text-muted mb-2">
        <i class="fas fa-spinner fa-spin"></i> Mencari data...
    </div>
    <div class="table-responsive" wire:loading.remove>
        <table class="table table-borderless table-striped table-hover mt-3">
            <thead class="alert-secondary">
                <tr>
                    <th>#</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>Nomor HP</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Foto KTP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($results as $index => $item)
                    <tr>
                        <td>{{ $results->firstItem() + $index }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->nama_lengkap, 30) }}</td>
                        <td>{{ $item->nik }}</td>
                        <td>{{ $item->nomor_pengadu }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item->alamat_lengkap, 50) }}</td>
                        <td>
                            @if($item->foto_ktp)
                                <a href="{{ $item->foto_ktp_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-id-card fa-sm"></i> Lihat
                                </a>
                            @else
                                <span class="text-muted">Tidak Ada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.laporan.create', ['identitas_id' => $item->id]) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-edit fa-sm"></i> Isi Laporan
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data antrian yang tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $results->links('admin.layouts.paginations') }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('start-auto-refresh', function () {
            setInterval(function () {
                Livewire.emit('refreshComponent');
            }, 10000);
        });
    });
</script>
@endpush
