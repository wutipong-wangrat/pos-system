<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Hash;

class User extends Component{
    public $showModal = false;
    public $showModalDelete = false;
    public $id;
    public $name;
    public $email;
    public $password;
    public $password_confirm;
    public $role = 'admin';
    public $listRole = ['admin', 'user'];
    public $status = 'active';
    public $listStatus = ['active', 'inactive'];
    public $listUser;
    public $nameForDelete;
    public $errorLengthPassword;
    public $mailError;
    public $error;

    public function mount(){
        $this->fetchData();
    }
    
    public function fetchData(){
        $this->listUser = UserModel::all();
    }

    public function openModal(){
        $this->showModal = true;
        $this->errorLengthPassword = null;
        $this->mailError = null;
        $this->error = null;
    }

    public function save(){
        if ($this->id != null) {
            $user = UserModel::find($this->id);
            $user->name = $this->name;
            $user->email = $this->email;
            $user->role = $this->role;
            $user->status = $this->status;
            $user->save();

            $this->fetchData();
            $this->showModal = false;
            return;
        }

        $userExists = UserModel::where('email', $this->email)->first();
        if ($userExists) {
            $this->mailError = 'อีเมลนี้มีผู้ใช้งานแล้ว';
            return;
        }

        if ($this->password != $this->password_confirm) {
            $this->error = 'รหัสผ่านไม่ตรงกัน';
            return;
        }
        // dd($this->name, $this->email, $this->password, $this->role, $this->status);

        $user = new UserModel();
        $password = Hash::make($this->password);
        
        $user->password = $password;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->role = $this->role;
        $user->status = $this->status;
        $user->save();

        
        $this->fetchData();
        $this->showModal = false;
    }

    public function checkPasswordLength(){
        if (strlen($this->password) < 4) {
            $this->errorLengthPassword = 'Password must be at least 4 characters long.';
        }
    }

    public function openModalEdit($id){
        $this->showModal = true;
        $this->id = $id;

        $user = UserModel::find($id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->status = $user->status;
    }

    public function updateUser(){
        $user = UserModel::find($this->id);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->role = $this->role;
        $user->status = $this->status;
        $user->save();

        $this->fetchData();
        $this->showModal = false;
    }

    public function openModalDelete($id, $name){
        $this->showModalDelete = true;
        $this->id = $id;
        $this->nameForDelete = $name;
    }

    public function delete(){
        UserModel::find($this->id)->delete();
        $this->fetchData();
        $this->showModalDelete = false;
    }
    
    public function render(){
        return view('livewire.user');
    }
}