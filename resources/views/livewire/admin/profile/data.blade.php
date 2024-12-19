<div>
    <div class="container-fluid mb-3">
        <div
            class="d-flex flex-column flex-sm-row align-items-center py-4 py-sm-3 px-3 bg-white rounded-3 border shadow-sm">
            <div class="img-profile">
                @if (auth('admin')->user()->avatar == 'sample-images.png')
                <div class="img-profile-content"
                    style="background-image: url('/images/avatar/{{auth('admin')->user()->avatar }}');">
                </div>
                @else
                <div class="img-profile-content"
                    style="background-image: url('/images/avatar/admin/{{auth('admin')->user()->avatar }}');">
                </div>
                @endif
                <label for="img-upload" class="btn position-absolute bottom-0 lh-sm py-0 w-100 text-white"
                    style="background-color: #ffffff95">
                    <i class="fas fa-upload m-0 p-0" style="font-size: 12px"></i>
                </label>
                <input wire:model='images' type="file" name="images" id="img-upload" onchange="uploadImages()"
                    class="d-none">
            </div>
            <div class="ms-sm-3 text-center text-sm-start">
                <p class="fs-4 fw-bold mb-0 text-capitalize">{{ auth('admin')->user()->username }}</p>
                <p class="mb-2">{{ auth('admin')->user()->email }}</p>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        <div class="d-block bg-white rounded-3 border shadow-sm">
            <div class="d-flex align-items-center py-2 px-3 border-bottom">
                <p class="mb-0 fw-bold text-color">Profile Detail</p>
                <div class="dropstart ms-auto">
                    <button class="btn btn-sm" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-bars fa-sm fa-fw"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        @if($edit == false)
                        <li><button wire:click='edit' class="dropdown-item" href="#">Edit</button></li>
                        @else
                        <li><button wire:click='save' class="dropdown-item" href="#">Simpan Perubahan</button></li>
                        <li><button wire:click='cancel' class="dropdown-item" href="#">Batalkan Perubahan</button></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="p-3">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input wire:model='username' type="text" class="form-control disabled" id="username" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input wire:model='jabatan' type="text" id="jabatan" class="form-control" @if ($edit==false) disabled @endif>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input wire:model='nama' type="text" id="nama" class="form-control" @if ($edit==false) disabled @endif>
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <input wire:model='unit' type="text" id="unit" class="form-control" @if ($edit==false) disabled @endif>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="deputi" class="form-label">Deputi</label>
                    <input wire:model='deputi' type="text" id="deputi" class="form-control" @if ($edit==false) disabled @endif>
                </div>
            </div>
        </div>
    </div>

    <script>
        function uploadImages(){
            Livewire.emit('images')
        }
        window.addEventListener('DOMContentLoaded', () => {
            window.addEventListener('success', event => {
                Swal.fire({
                    icon: 'success',
                    title: 'Good Jobs!',
                    text: event.detail,
                })
            })
            window.addEventListener('erros', event => {
                Swal.fire({
                    icon: 'error',
                    title: 'Opps...!',
                    text: event.detail,
                })
            })
        });

    </script>
</div>