<div class="container-fluid mb-3">
    <div class="d-block bg-white rounded-3 border shadow-sm">
        <div class="d-flex align-items-center py-2 px-3 border-bottom">
            <p class="mb-0 fw-bold text-color">API Settings</p>
            <div class="dropstart ms-auto">
                <button class="btn btn-sm" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bars fa-sm fa-fw"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                    @if($edit == false)
                        <li><button wire:click='edit' class="dropdown-item">Edit</button></li>
                    @else
                        <li><button wire:click='save' class="dropdown-item">Simpan</button></li>
                        <li><button wire:click='cancel' class="dropdown-item">Batal</button></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="p-3">
            <div class="mb-3">
                <label class="form-label">Base URL</label>
                <input type="text" wire:model="settings.base_url" class="form-control" @if(!$edit) disabled @endif>
            </div>
            <div class="mb-3">
                <label class="form-label">Token</label>
                <input type="text" wire:model="settings.token" class="form-control" @if(!$edit) disabled @endif>
            </div>
            <div class="mb-3">
                <label class="form-label">Auth</label>
                <input type="text" wire:model="settings.auth" class="form-control" @if(!$edit) disabled @endif>
            </div>
        </div>
    </div>
</div>