<div>
    <div class="container-fluid">
        <div class="d-flex flex-column flex-sm-row align-items-center bg-white rounded-3 border shadow-sm p-3">
            <div class="p-0 p-sm-3" style="width: 250px">
                <img src="{{ url('/images/elements/password.png') }}" alt="" class="img-fluid">
            </div>
            <div class="ms-0 ms-md-4 mb-3">
                <p class="fs-5 fw-bold text-color">Change your password account</p>
                <p class="text-secondary mb-0">Your Account</p>
                <p class="mb-4 text-color">{{ auth('admin')->user()->email }}</p>
                <button wire:click='show' type="button" class="btn btn-blue rounded-0">Set Password</button>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="mb-3 text-center py-3">
                        <img src="{{ url('/images/elements/password.png') }}" alt="" class="img-fluid w-50 mb-3">
                        <p class="fs-4 fw-bold text-color">Rubah Password</p>
                    </div>
                    <div class="mb-3">
                        <label for="question" class="form-label">Password</label>
                        <input wire:model='password' type="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="answer" class="form-label">Confirmasi password</label>
                        <input wire:model='confirmation' type="password" class="form-control @error('password') is-invalid @enderror">
                        @error('confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button wire:click='setup' type="button" class="btn btn-blue">Simpan</button>
                  </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('pModalShow', () => {
            $('#passwordModal').modal('show');
        });
        document.addEventListener('pModalExpand', () => {
            $('#passwordModal').modal('hide');
        });
        document.addEventListener('deleteConfrimed', function() {
            Swal.fire({
                    title: "Delete?",
                    text: "Are you sure to delete this q&a?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete!',
                    cancelButtonText: 'Tidak',
                })
                .then((next) => {
                    if (next.isConfirmed) {
                        Livewire.emit('deleteAction');
                    } else {
                        Swal.fire("Data anda tetap aman!");
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
