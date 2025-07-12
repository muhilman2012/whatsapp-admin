<div>
    <input type="text" wire:model.debounce.500ms="search" class="form-control mb-3" placeholder="Cari berdasarkan tiket atau nama">

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nomor Tiket</th>
                    <th>Nama Pengadu</th>
                    <th>Instansi Tujuan</th>
                    <th>Tanggal Diteruskan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                <tr>
                    <td>{{ $data->firstItem() + $index }}</td>
                    <td>{{ $item->laporan->nomor_tiket }}</td>
                    <td>{{ $item->laporan->nama_lengkap }}</td>
                    <td>{{ $item->institution->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->sent_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.laporan.followup', ['nomor_tiket' => $item->laporan->nomor_tiket]) }}" target="_blank" class="btn btn-sm btn-primary">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $data->links('pagination::bootstrap-4') }}
</div>