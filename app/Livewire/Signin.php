<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Signin extends Component
{
    public $username;
    public $password;
    public $errorUsername;
    public $errorPassword;
    public $error = null;

    public function signin()
    {
        $this->errorUsername = null;
        $this->errorPassword = null;
        $this->error = null;

        $validator = Validator::make([
            'username' => $this->username,
            'password' => $this->password,
        ], [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->errorUsername = $validator->errors()->get('username')[0] ?? null;
            $this->errorPassword = $validator->errors()->get('password')[0] ?? null;
        } else {
            $user = User::where('name', $this->username)->first();
            $this->username = null;

            if ($user && Hash::check($this->password, $user->password)) {
                session()->put('user_id', $user->id);
                session()->put('user_name', $user->name);
                // Auth::login($user);
                $user_role = $user->role;

                if ($user_role == 'admin') {
                    return redirect('/dashboard');
                } else {
                    return redirect('/order');
                }
            } else {
                $this->error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
            }
        }
    }

    public function render()
    {
        return view('livewire.signin');
    }
}
