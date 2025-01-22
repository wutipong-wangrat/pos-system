<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AutoLoginController extends Controller{
    public function customerLogin() {
        $user = User::where('name', 'customer')->first();

        if ($user && Hash::check('1234', $user->password)) {
            session()->put('user_id', $user->id);
            session()->put('user_name', $user->name);
            return redirect('/order');
        }

        return redirect('/')->with('addError', 'ไม่สามารถเข้าสู่ระบบได้');
    }
}