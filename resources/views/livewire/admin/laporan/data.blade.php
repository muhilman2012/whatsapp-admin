<div>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <!-- <form method="GET" action="{{ route('admin.laporan.export') }}" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="end_date">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control">
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">Export Data</button>
                </div>
            </div>
        </form> -->
        <!-- Bagian Export Data -->
        <div class="d-flex">
            <form action="{{ route('admin.laporan.export.tanggal') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Export Data</button>
                </div>
            </form>
        </div>
        <!-- Bagian Cari dan Pengaturan Halaman -->
        <div class="d-flex align-items-center">
            <!-- Input Cari -->
            <div>
                <input wire:model="search" type="text" class="form-control" placeholder="Cari...">
            </div>
            <!-- Select Jumlah Halaman -->
            <div class="ms-2">
                <select wire:model="pages" class="form-select">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="99999999999">All</option>
                </select>
            </div>
        </div>
    </div>
    <div class="table-responsive" wire:loading.remove wire:target='search'>
        <table class="table table-borderless table-striped table-hover mt-3">
            <thead class="alert-secondary">
                <tr>
                    <th scope="col">#</th>
                    <th>Nomor Tiket</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>No Telp</th>
                    <th>JK</th>
                    <th>Judul Pengaduan</th>
                    <th>Status</th>
                    <th>Dikirim</th>
                    <th>Sisa Hari</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $item->nomor_tiket }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($item->nama_lengkap, 20) }}</td>
                    <td>{{ $item->nik }}</td>
                    <td>{{ $item->nomor_pengadu }}</td>
                    <td>{{ $item->jenis_kelamin }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($item->judul, 20) }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($item->status, 10) }}</td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>{{ $item->sisa_hari }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.laporan.detail', ['nomor_tiket' => $item->nomor_tiket]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eye fa-sm fa-fw"></i>
                        </a>
                        <a href="{{ route('admin.laporan.edit', ['nomor_tiket' => $item->nomor_tiket]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-pencil-alt fa-sm fa-fw"></i>
                        </a>
                        <button wire:click="removed({{ $item->nomor_tiket }})" type="button" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash fa-sm fa-fw"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border rounded p-5 mb-3" wire:loading.block wire:target='search'>
		<div class="d-flex justify-content-center mb-4">
			<div class="spinner-border" role="status">
			  <span class="visually-hidden">Loading...</span>
			</div>
		</div>
		<p class="fw-bold fs-5 text-center m-0">Loading...</p>
	</div>
    <div class="d-flex align-items-center">
        <p class="mb-0 border py-1 px-2 rounded">
            <span class="fw-bold">{{ $data->count() }}</span>
        </p>
        @if ($data->hasPages())
        <nav class="ms-auto">
            {{ $data->links('admin.layouts.paginations') }}
        </nav>
        @endif
    </div>


    <script>
        document.addEventListener('deleteConfirmed', function() {
            Swal.fire({
                    title: "Hapus?",
                    text: "Yakin Data Hapus Pengaduan?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Tidak',
                })
                .then((next) => {
                    if (next.isConfirmed) {
                        Livewire.emit('deleteAction');
                    } else {
                        Swal.fire("Data Pengaduan tetap Aman!");
                    }
                });
        })
    </script>

    @if(session()->has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Good Jobs!',
            text: '{{ session()->get("success") }}',
            showConfirmButton: false,
            timer: 2500
        })
        location.reload();
    </script>
    @elseif(session()->has('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Opps...!',
            text: '{{ session()->get("error") }}',
            showConfirmButton: false,
            timer: 2500
        })
    </script>
    @endif
</div>