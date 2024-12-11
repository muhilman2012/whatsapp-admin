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
        <!-- Bagian Export Data -->
        <div class="d-flex">
            <!-- Export Data Berdasarkan Tanggal -->
            <form action="{{ route('admin.laporan.export.tanggal') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Export Data</button>
                </div>
            </form>

            <!-- Export All Data -->
            <div class="ms-2">
                <form action="{{ route('admin.laporan.export.all') }}" method="GET">
                    <button type="submit" class="btn btn-success">Export All Data</button>
                </form>
            </div>
        </div>
        <!-- Bagian Cari, Filter Kategori, dan Pengaturan Halaman -->
        <div class="d-flex align-items-center">
            @if (in_array(auth('admin')->user()->role, ['admin', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4', 'asdep']))
            <div class="ms-2">
                <select wire:model="filterAssignment" class="form-select">
                    <option value="">-- Semua Data --</option>
                    <option value="unassigned">Belum Ter-assign</option>
                    <option value="assigned">Sudah Ter-assign</option>
                </select>
            </div>
            @endif
            <!-- Tombol Import -->
            @if (auth('admin')->user()->role === 'admin')
            <div class="ms-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">Import</button>
            </div>
            @endif
            <!-- Input Cari -->
            <div>
                <input wire:model="search" type="text" class="form-control ms-2" placeholder="Cari...">
            </div>
            <!-- Select Kategori -->
            <div class="ms-2">
                <select wire:model="filterKategori" class="form-select ms-2" style="width: 200px;">
                    <option value="" selected>Semua Kategori</option>
                    <optgroup label="SP4N Lapor">
                        @foreach ($kategoriSP4NLapor as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Kategori Baru">
                        @foreach ($kategoriBaru as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </optgroup>
                </select>
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

    <!-- Tabel Data -->
    <div class="table-responsive" wire:loading.remove wire:target='search'>
        <table class="table table-borderless table-striped table-hover mt-3">
            <thead class="alert-secondary">
                <tr>
                    <th scope="col">
                        <input type="checkbox" wire:model="selectAll">
                    </th>
                    <th scope="col">#</th>
                    <th>Nomor Tiket</th>
                    <th>Nama Lengkap</th>
                    <th>Judul Pengaduan</th>
                    <th>Kategori</th>
                    <th>Disposisi</th>
                    <th>Dikirim</th>
                    <th>Sisa Hari</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                <tr>
                    <td>
                        <input type="checkbox" wire:model="selected" value="{{ $item->id }}">
                    </td>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $item->nomor_tiket }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($item->nama_lengkap, 20) }}</td>
                    <td>{{ \Illuminate\Support\Str::words($item->judul, 20) }}</td>
                    <td>{{ \Illuminate\Support\Str::words($item->kategori, 4) }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($item->disposisi, 10) }}</td>
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

    <div class="d-flex align-items-center mt-3">
        <p class="mb-0 border py-1 px-2 rounded">
            <span class="fw-bold">{{ $data->count() }}</span> Data
        </p>

        @if ($selected)
            <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#modalKategori">
                Update Kategori
            </button>
            <button type="button" class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#modalDisposisi">
                Update Disposisi
            </button>
        @if (in_array(auth('admin')->user()->role, ['admin', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4', 'asdep']))
            <button type="button" class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                Assign to Analis
            </button>
        @endif
        @endif

        @if ($data->hasPages())
        <nav class="ms-auto">
            {{ $data->links('admin.layouts.paginations') }}
        </nav>
        @endif
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.laporan.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload File Excel</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modalKategoriLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKategoriLabel">Pilih Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select wire:model="selectedKategori" class="form-control">
                        <option value="" selected>Pilih Kategori</option>
                        <optgroup label="SP4N Lapor">
                            @foreach ($kategoriSP4NLapor as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Kategori Baru">
                            @foreach ($kategoriBaru as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="updateKategoriMassal">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDisposisi" tabindex="-1" aria-labelledby="modalDisposisiLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDisposisiLabel">Pilih Disposisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select wire:model="selectedDisposisi" class="form-control">
                        <option value="" selected>Pilih Disposisi</option>
                        @foreach ($namaDeputi as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="updateDisposisiMassal">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <form wire:submit.prevent="assignToAnalis">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignModalLabel">Assign ke Analis</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <select wire:model="selectedAnalis" class="form-control">
                            <option value="">Pilih Analis</option>
                            @foreach($analisList as $analis)
                                <option value="{{ $analis->id_admins }}">{{ $analis->username }}</option>
                            @endforeach
                        </select>
                        <textarea wire:model.defer="assignNotes" class="form-control mt-3" placeholder="Catatan untuk analis"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </div>
            </form>
        </div>
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