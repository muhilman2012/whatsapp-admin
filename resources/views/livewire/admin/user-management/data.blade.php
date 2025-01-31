<div>
    <!-- Pesan Flash -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex mb-3 justify-content-between align-items-center">
        <div class="d-flex">
            <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah User</button>
        </div>
        <div class="d-flex align-items-center">
            <div class="d-flex ms-2">
                <input wire:model="search" type="text" class="form-control" placeholder="Cari...">
            </div>
            <div class="d-flex ms-2">
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
                    <th scope="col">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Jabatan</th>
                    <th>Unit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->role }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.user_management.edit', $item->id_admins) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-pencil-alt fa-sm fa-fw"></i>
                        </a>
                        <button wire:click="removed({{ $item->id_admins }})" type="button" class="btn btn-outline-danger btn-sm">
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

        @if ($data->hasPages())
        <nav class="ms-auto">
            {{ $data->links('admin.layouts.paginations') }}
        </nav>
        @endif
    </div>

    <!-- Modal Tambah User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <form wire:submit.prevent="addUser">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" wire:model="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" wire:model="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" wire:model="role" required>
                                <option>-- Pilih Role --</option>
                                <option value="superadmin">Super Admin</option>
                                <option value="admin">Admin</option>
                                <option value="deputi_1">Deputi 1</option>
                                <option value="deputi_2">Deputi 2</option>
                                <option value="deputi_3">Deputi 3</option>
                                <option value="deputi_4">Deputi 4</option>
                                <option value="asdep">Asdep</option>
                                <option value="analis">Analis</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" wire:model="jabatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="deputi" class="form-label">Deputi</label>
                            <select class="form-select" id="deputi" name="deputi" wire:model="deputi" required>
                                <option>-- Pilih Deputi --</option>
                                <option value="Admin">Admin</option>
                                <option value="Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital">Deputi Bidang Dukungan Kebijakan Perekonomian, Pariwisata, dan Transformasi Digital</option>
                                <option value="Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia">Deputi Bidang Dukungan Kebijakan Peningkatan Kesejahteraan Dan Pembangunan Sumber Daya Manusia</option>
                                <option value="Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan">Deputi Bidang Dukungan Kebijakan Pemerintahan dan Pemerataan Pembangunan</option>
                                <option value="Deputi Bidang Administrasi">Deputi Bidang Administrasi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <select class="form-select" id="unit" name="unit" wire:model="unit" required>
                                <option>-- Pilih Unit --</option>
                                <option value="Admin">Admin</option>
                                <option value="Asisten Deputi Ekonomi, Keuangan, dan Transformasi Digital">Asisten Deputi Ekonomi, Keuangan, dan Transformasi Digital</option>
                                <option value="Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif">Asisten Deputi Industri, Perdagangan, Pariwisata, dan Ekonomi Kreatif</option>
                                <option value="Asisten Deputi Infrastruktur, Sumber Daya Alam, dan Pembangunan Kewilayahan">Asisten Deputi Infrastruktur, Sumber Daya Alam, dan Pembangunan Kewilayahan</option>
                                <option value="Asisten Deputi Pengentasan Kemiskinan dan Pembangunan Desa">Asisten Deputi Pengentasan Kemiskinan dan Pembangunan Desa</option>
                                <option value="Asisten Deputi Kesehatan, Gizi, dan Pembangunan Keluarga">Asisten Deputi Kesehatan, Gizi, dan Pembangunan Keluarga</option>
                                <option value="Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana">Asisten Deputi Pemberdayaan Masyarakat dan Penanggulangan Bencana</option>
                                <option value="Asisten Deputi Pendidikan, Agama, Kebudayaan, Pemuda, dan Olahraga">Asisten Deputi Pendidikan, Agama, Kebudayaan, Pemuda, dan Olahraga</option>
                                <option value="Asisten Deputi Hubungan Luar Negeri dan Pertahanan">Asisten Deputi Hubungan Luar Negeri dan Pertahanan</option>
                                <option value="Asisten Deputi Politik, Keamanan, Hukum, dan Hak Asasi Manusia">Asisten Deputi Politik, Keamanan, Hukum, dan Hak Asasi Manusia</option>
                                <option value="Asisten Deputi Tata Kelola Pemerintahan dan Percepatan Pembangunan Daerah">Asisten Deputi Tata Kelola Pemerintahan dan Percepatan Pembangunan Daerah</option>
                                <option value="Biro Tata Usaha dan Sumber Daya Manusia">Biro Tata Usaha dan Sumber Daya Manusia</option>
                                <option value="Biro Perencanaan dan Keuangan">Biro Perencanaan dan Keuangan</option>
                                <option value="Biro Umum">Biro Umum</option>
                                <option value="Biro Protokol dan Kerumahtanggaan">Biro Protokol dan Kerumahtanggaan</option>
                                <option value="Biro Pers, Media, dan Informasi">Biro Pers, Media, dan Informasi</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('closeModal', function () {
            var modal = new bootstrap.Modal(document.getElementById('addUserModal'));
            modal.hide(); // Menyembunyikan modal
        });
    </script>
    <script>
        document.addEventListener('deleteConfirmed', function() {
            Swal.fire({
                    title: "Hapus?",
                    text: "Yakin Hapus User?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Tidak',
                })
                .then((next) => {
                    if (next.isConfirmed) {
                        Livewire.emit('deleteAction');
                    } else {
                        Swal.fire("Data User tetap Aman!");
                    }
                });
        })
    </script>

    @if(session()->has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
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
            title: 'Maaf..',
            text: '{{ session()->get("error") }}',
            showConfirmButton: false,
            timer: 2500
        })
    </script>
    @endif
</div>