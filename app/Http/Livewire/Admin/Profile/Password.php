<?php

namespace App\Http\Livewire\Admin\Profile;

use App\Models\admins;
use Livewire\Component;

class Password extends Component
{
    public $password, $confirmation;

    protected $rules = [
        'password'  => 'required',
        'confirmation'  => 'required'
    ];
    protected $messages = [
        'password.required' => 'Oops, password tidak boleh kosong!',
        'confirmation.required' => 'Oops, konfirmasi password tidak boleh kosong!',
    ];

    public function updated()
    {
        $this->validate();
    }

    public function show(){
        $this->password = '';
        $this->confirmation = '';
        $this->dispatchBrowserEvent('pModalShow');
    }

    public function setup(){
        $id = auth('admin')->user()->id_admins;
        if($this->password != $this->confirmation){
            session()->flash('error', 'Oops, password dan konfrimasi password tidak sama!');
        } else {
            $data = admins::find($id);
            $data->password = bcrypt($this->password);
            if($data->save()){
                session()->flash('success', 'Password berhasil dirubah!');
            }else{
                session()->flash('error', 'Oops, maaf database sedang sibuk!');
            }   
        }
    }

    public function render()
    {
        return view('livewire.admin.profile.password');
    }
}
