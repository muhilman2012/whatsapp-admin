<div>
    <!-- Notifikasi -->
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter dan Search -->
    <div class="d-flex justify-content-between mb-3">
        <!-- Input Search -->
        <input wire:model="search" type="text" class="form-control w-25" placeholder="Cari berdasarkan tiket, judul, nama...">

        <!-- Pilihan Jumlah Halaman -->
        <select wire:model="pages" class="form-select w-auto">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>

    <!-- Tabel Data -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nomor Tiket</th>
                    <th>Nama Lengkap</th>
                    <th>Judul</th>
                    <th>Status Analisis</th>
                    <th>Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nomor_tiket }}</td>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>{{ $item->judul }}</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>{{ $item->created_at->format('d-m-Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data dengan status Pending.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between mt-3">
        <p>Total Data: {{ $data->total() }}</p>
        {{ $data->links() }}
    </div>
</div>
