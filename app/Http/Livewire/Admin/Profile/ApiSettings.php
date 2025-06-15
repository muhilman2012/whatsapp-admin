<?php

namespace App\Http\Livewire\Admin\Profile;

use Livewire\Component;
use App\Models\ApiSetting;

class ApiSettings extends Component
{
    public $settings = [];
    public $edit = false;

    public function mount()
    {
        $this->settings = ApiSetting::pluck('value', 'key')->toArray();
    }

    public function edit()
    {
        $this->edit = true;
    }

    public function cancel()
    {
        $this->edit = false;
        $this->mount(); // reload data
    }

    public function save()
    {
        foreach ($this->settings as $key => $value) {
            ApiSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->edit = false;
        $this->dispatchBrowserEvent('success', 'Pengaturan API berhasil disimpan.');
    }

    public function render()
    {
        if (auth('admin')->user()->role !== 'superadmin') {
            abort(403);
        }

        return view('livewire.admin.profile.api-settings');
    }
}
