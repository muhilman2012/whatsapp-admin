<div>
    <div class="d-flex mb-3">
        <input type="text" wire:model="search" class="form-control" placeholder="Cari...">
        <select wire:model="pages" class="form-select ms-2" style="width: 150px;">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nomor Tiket</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Disposisi Terbaru</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $laporan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $laporan->nomor_tiket }}</td>
                    <td>{{ $laporan->judul }}</td>
                    <td>{{ $laporan->kategori }}</td>
                    <td>{{ $laporan->disposisi_terbaru }}</td>
                    <td>
                        <a href="{{ route('admin.laporan.detail', $laporan->nomor_tiket) }}" class="btn btn-sm btn-primary">Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $data->links() }}
    </div>
</div>