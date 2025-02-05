<div>
    <!-- Pesan Flash -->
    @if (session('message'))
        <div class="alert alert-info">
            {!! session('message') !!}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {!! session('success') !!}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <!-- Bagian Cari, Filter Kategori, dan Pengaturan Halaman -->
        <div class="d-flex align-items-center">
            <!-- Input Cari -->
            <div class="me-2">
                <input wire:model="search" type="text" class="form-control ms-2" placeholder="Cari...">
            </div>
            <!-- Filter Status -->
            <div class="ms-2">
                <select wire:model="filterStatus" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Penanganan Selesai">Selesai</option>
                    <option value="Menunggu kelengkapan data dukung dari Pelapor">Kurang Berkas</option>
                    <option value="Diteruskan kepada instansi yang berwenang untuk penanganan lebih lanjut">Tindak Lanjut K/L</option>
                    <option value="Proses verifikasi dan telaah">Verifikasi</option>
                </select>
            </div>
            @if (in_array(auth('admin')->user()->role, ['superadmin' ,'admin', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4', 'asdep']))
            <!-- Filter Assignment -->
            <div class="ms-2">
                <select wire:model="filterAssignment" class="form-select">
                    <option value="">Semua Data</option>
                    <option value="unassigned">Belum Terdisposisi</option>
                    <option value="assigned">Sudah Terdisposisi</option>
                    @if (in_array(auth('admin')->user()->role, ['superadmin' ,'admin',]))
                    <option value="unassigned_disposition">Belum Terdistribusi</option>
                    @endif
                </select>
            </div>
            @endif

            <!-- Filter Status Analisis -->
            <div class="ms-2">
                <select wire:model="filterStatusAnalisis" class="form-select">
                    <option value="">Semua Status Analisis</option>
                    <option value="Menunggu Telaahan">Menunggu Telaahan</option>
                    <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                    <option value="Disetujui">Disetujui</option>
                    <option value="Perbaikan">Perbaikan</option>
                </select>
            </div>
            
            <!-- Input Tanggal -->
            <div class="col-auto ms-2">
                <input wire:model="tanggal" type="date" name="tanggal" id="tanggal" class="form-control" required>
            </div>
            <div class="ms-2">
                <select wire:model="sumber_pengaduan" class="form-select">
                    <option value="">Sumber</option>
                    <option value="whatsapp">WA</option>
                    <option value="tatap muka">TM</option>
                </select>
            </div>
            <!-- Export Filtered Data -->
            <div class="ms-2">
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" type="button" id="exportFilteredDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Export Filtered Data
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportFilteredDropdown">
                        <li>
                            <form action="{{ route('admin.laporan.export.filtered.excel') }}" method="GET">
                                <input type="hidden" name="filterKategori" value="{{ $filterKategori }}">
                                <input type="hidden" name="filterStatus" value="{{ $filterStatus }}">
                                <input type="hidden" name="search" value="{{ $search }}">
                                <input type="hidden" name="filterAssignment" value="{{ $filterAssignment }}">
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                <button type="submit" class="dropdown-item">Export to Excel</button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('admin.laporan.export.filtered.pdf') }}" method="GET">
                                <input type="hidden" name="filterKategori" value="{{ $filterKategori }}">
                                <input type="hidden" name="filterStatus" value="{{ $filterStatus }}">
                                <input type="hidden" name="search" value="{{ $search }}">
                                <input type="hidden" name="filterAssignment" value="{{ $filterAssignment }}">
                                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                <button type="submit" class="dropdown-item">Export to PDF</button>
                            </form>
                        </li>
                    </ul>
                </div>
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
            <!-- Select Urutan -->
            <div class="ms-2">
                <select wire:model="sortDirection" class="form-select">
                    <option value="desc">Terbaru</option>
                    <option value="asc">Terlama</option>
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
                        <input type="checkbox" wire:model="selectAll"
                            @if(count(array_intersect($currentPageData, $selected)) === count($currentPageData)) 
                                checked 
                            @endif>
                    </th>
                    <th scope="col">#</th>
                    <th>Nomor Tiket</th>
                    <th>Nama Lengkap</th>
                    <th>Judul Pengaduan</th>
                    <th>Kategori</th>
                    <th>Distribusi asal</th>
                    <th>Distribusi tujuan</th>
                    <th>
                        @if (in_array(auth('admin')->user()->role, ['superadmin', 'admin', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4', 'asdep']))
                            Disposisi ke
                        @endif
                    </th>
                    <th>Sumber</th>
                    <th>Status</th>
                    <th>Dikirim</th>
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
                    <td>{{ $item->disposisi }}</td>
                    <td>{{ $item->disposisi_terbaru}}</td>
                    <td>  
                        @if (in_array(auth('admin')->user()->role, ['superadmin', 'admin', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4', 'asdep']))  
                            <!-- Menampilkan nama analis yang diberi tugas -->  
                            @php  
                                $assignedTo = $item->assignments->where('laporan_id', $item->id)->first();  
                            @endphp  
                            @if ($assignedTo)  
                                <span>{{ $assignedTo->assignedTo->nama }}</span> <!-- Nama analis ditampilkan dalam warna hitam -->  
                            @else  
                                <span class="text-danger">Belum terdisposisi</span> <!-- Teks merah jika belum terdisposisi -->  
                            @endif  
                        @endif  
                    </td>
                    <td>
                        @if($item->sumber_pengaduan === 'tatap muka')
                            <span class="badge bg-primary">TM</span>
                        @elseif($item->sumber_pengaduan === 'whatsapp')
                            <span class="badge bg-success">WA</span>
                        @else
                            <span class="badge bg-secondary">{{ $item->sumber_pengaduan }}</span>
                        @endif
                    </td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.laporan.detail', ['nomor_tiket' => $item->nomor_tiket]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eye fa-sm fa-fw"></i>
                        </a>
                        <a href="{{ route('admin.laporan.edit', ['nomor_tiket' => $item->nomor_tiket]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-pencil-alt fa-sm fa-fw"></i>
                        </a>
                        @if (auth('admin')->user()->role === 'superadmin')
                        <button wire:click="removed({{ $item->nomor_tiket }})" type="button" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash fa-sm fa-fw"></i>
                        </button>
                        @endif
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
                Ubah Kategori
            </button>
            <!-- <button type="button" class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#modalDisposisi">
                Update Disposisi
            </button> -->
        @if (in_array(auth('admin')->user()->role, ['superadmin' ,'admin', 'deputi_1', 'deputi_2', 'deputi_3', 'deputi_4', 'asdep']))
            <button type="button" class="btn btn-info ms-2" data-bs-toggle="modal" data-bs-target="#assignModal">
                Disposisi ke Analis
            </button>
            <button type="button" class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#pelimpahanModal">
                Limpahkan
            </button>
        @endif
        @endif

        @if ($data->hasPages())
        <nav class="ms-auto">
            {{ $data->links('admin.layouts.paginations') }}
        </nav>
        @endif
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
                        @foreach ($kategoriDeputi as $deputi => $kategoris)
                            @if(isset($namaDeputi[$deputi]))
                                <optgroup label="{{ $namaDeputi[$deputi] }}">
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori }}">{{ $kategori }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="confirmUpdateKategori">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Perubahan Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Anda akan merubah kategori menjadi "{{ $selectedKategori }}" dan akan terdisposisi ke "{{ $namaDeputi[$selectedDeputi] ?? 'Tidak diketahui' }}". Apakah Anda yakin?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="updateKategoriMassal">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign to Analis -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Disposisi ke Analis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- List Analis -->
                    <div class="mb-3">
                        <label for="selectedAnalis" class="form-label">Pilih Analis</label>
                        <select wire:model="selectedAnalis" class="form-select" id="selectedAnalis">
                            <option value="">Pilih Analis</option>
                            @foreach($analisList as $analis)
                                <option value="{{ $analis->id_admins }}">
                                    {{ $analis->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-3">
                        <label for="assignNotes" class="form-label">Catatan</label>
                        <textarea wire:model="assignNotes" class="form-control" id="assignNotes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" wire:click="assignToAnalis" class="btn btn-primary">Disposisikan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pelimpahanModal" tabindex="-1" aria-labelledby="pelimpahanModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <form wire:submit.prevent="pelimpahan">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pelimpahanModalLabel">Pelimpahan Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <select wire:model="selectedDisposisi" class="form-control">
                            <option value="">Pilih Disposisi Baru</option>
                            @foreach ($namaDeputi as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Pelimpahan</button>
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

    <!-- Script untuk memeriksa status export PDF -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkExportStatus = () => {
                fetch('{{ route('admin.laporan.checkExportStatus') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        file_name: '{{ session('fileName') }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.ready) {
                        window.location.href = data.download_url;
                    } else {
                        setTimeout(checkExportStatus, 5000); // Cek setiap 5 detik
                    }
                })
                .catch(error => console.error('Error:', error));
            };

            <?php if (session('message') && strpos(session('message'), 'Proses ekspor PDF sedang berjalan') !== false): ?>
                checkExportStatus();
            <?php endif; ?>
        });
    </script>
    <script>
        // JavaScript untuk Export PDF dengan proses Asinkron
        document.querySelectorAll('.export-pdf-btn').forEach(button => {
            button.addEventListener('click', async () => {
                const dateInput = document.querySelector('#tanggal');
                const selectedDate = dateInput.value;

                if (!selectedDate) {
                    alert('Tanggal harus dipilih!');
                    return;
                }

                const url = button.getAttribute('data-url');
                
                // Kirim permintaan ekspor PDF
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ tanggal: selectedDate })
                    });

                    if (response.ok) {
                        alert('Proses ekspor PDF sedang berjalan. File akan diunduh otomatis saat selesai.');
                        
                        const fileName = 'laporan_tanggal_' + selectedDate + '.pdf'; // Menentukan nama file

                        // Polling untuk cek apakah file sudah siap
                        const interval = setInterval(async () => {
                            const checkResponse = await fetch(`/admin/check-export-status?file_name=${fileName}`);
                            const status = await checkResponse.json();

                            if (status.ready) {
                                clearInterval(interval);

                                // Auto-download file jika sudah siap
                                const link = document.createElement('a');
                                link.href = status.download_url;
                                link.download = fileName;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);

                                alert('File berhasil diunduh!');
                            }
                        }, 5000); // Periksa setiap 5 detik
                    } else {
                        alert('Terjadi kesalahan saat mengirim permintaan ekspor.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat melakukan ekspor PDF.');
                }
            });
        });
    </script>
    <script>
        window.addEventListener('show-confirmation-modal', event => {
            $('#confirmationModal').modal('show');
        });
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