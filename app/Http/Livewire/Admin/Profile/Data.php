<?php

namespace App\Http\Livewire\Admin\Profile;

use App\Models\admins;
use Livewire\Component;
use Livewire\WithFileUploads;

class Data extends Component
{
    use WithFileUploads;
    public $id_admins, $username, $email, $phone, $born, $address, $avatar;
    public $images;
    public $edit = false;

    protected $listeners = ['images' => 'upload'];

    protected $rules = [
        'username'     => 'required',
    ];

    protected $messages = [
        'username.required' => 'Oops, username kamu tidak boleh kosong!',
    ];

    public function mount()
    {
        $this->id_admins = auth('admin')->user()->id_admins;
        $this->username = auth('admin')->user()->username;
        $this->email = auth('admin')->user()->email;
        $this->phone = auth('admin')->user()->phone;
        $this->born = auth('admin')->user()->born;
        $this->address = auth('admin')->user()->address;
        $this->avatar = auth('admin')->user()->avatar;
    }

    public function edit()
    {
        $this->edit = true;
    }

    public function cancle()
    {
        $this->username = auth('admin')->user()->username;
        $this->email = auth('admin')->user()->email;
        $this->phone = auth('admin')->user()->phone;
        $this->born = auth('admin')->user()->born;
        $this->address = auth('admin')->user()->address;
        $this->avatar = auth('admin')->user()->avatar;
        $this->edit = false;
    }

    public function save()
    {
        $this->validate();
        $data = admins::find($this->id_admins);
        $data->username = $this->username;
        $data->phone = $this->phone;
        $data->born = $this->born;
        $data->address = $this->address;
        $data->avatar = $this->avatar;
        if ($data->save()) {
            $this->edit = false;
            $this->dispatchBrowserEvent('success', 'Data berhasil diperbaharui!');
        } else {
            $this->edit = false;
            $this->dispatchBrowserEvent('errors', 'Database Error, data Gagal terupdate!!!');
        }
    }

    public function upload()
    {
        $this->validate([
            'images'  => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);
        
        $id = auth('admin')->user()->id_admins;
        $resorces = $this->images;
        $extension = $resorces->getClientOriginalExtension();
        $FileName = "AVT-" . $id . date("YmdHis") . '.' . $extension;

        $data = admins::find($id);
        $data->avatar = $FileName;
        if ($data->save()) {
            $resorces->storeAs('/images/avatar/admin/',  $FileName, 'myLocal');
            $this->dispatchBrowserEvent('success', 'Foto profile berhasil diperbaharui!');
        } else {
            $this->dispatchBrowserEvent('errors', 'Database Error, data Gagal terupdate!!!');
        }
    }

    public function render()
    {
        return view('livewire.admin.profile.data');
    }
}
