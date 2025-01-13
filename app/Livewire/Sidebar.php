<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Sidebar extends Component
{
    public $currentMenu = '';
    public $user_name;
    public $user_email;
    public $user_role = '';
    public $showModal = false;
    public $showModalEdit = false;
    public $username;
    public $password;
    public $password_confirm;

    protected $rules = [
        'username' => 'required',
        'password' => 'required',
        'password_confirm' => 'required',
    ];

    public function mount()
    {
        $user = User::where('status', 'active')->where('id', session()->get('user_id'))->first();
        // $user_id = session()->get('user_id');

        if (!$user) {
            return redirect()->to('/')->with('addError', 'You are not authorized to access this page.');
        }

        $this->currentMenu = session()->get('current_menu') ?? '';

        $currentPath = request()->path();

        $currentPath = trim($currentPath, '/');
        $firstSegment =  explode('/', $currentPath)[0];

        $validMenus = ['dashboard', 'categories', 'products', 'users', 'order', 'history'];

        if (in_array($firstSegment, $validMenus)) {
            $this->currentMenu = $firstSegment;
            session()->put('current_menu', $firstSegment);
        } else {
            $this->currentMenu = session('current_menu', 'dashboard');
        }
    }

    public function changeMenu($menu)
    {
        session()->put('current_menu', $menu);
        $this->currentMenu = $menu;

        return redirect()->to('/' . $menu);
    }

    public function editProfile()
    {
        $this->showModalEdit = true;

        $user = User::find(session()->get('user_id'));
        $this->username = $user->name;
        $this->saveSuccess = false;
    }

    public function updateProfile()
    {
        // dd($this->img);
        $this->validate([
            'username' => 'required',
            'password' => 'required',
            'password_confirm' => 'required',
        ]);

        try {
            $user = User::find(session()->get('user_id'));
            if ($this->username == null) {
                $this->addError('username', 'กรุณากรอกชื่อผู้ใช้');
                return;
            }

            if ($this->password != $this->password_confirm) {
                $this->addError('password', 'รหัสผ่านไม่ตรงกัน');
                return;
            }

            $user->name = $this->username;
            $user->password = $this->password ?? $user->password;
            // $user->img = $this->img;
            $user->save();
            session()->flash('success', 'อัพเดทข้อมูลสําเร็จ');
        } catch (\Exception $e) {
            session()->flash('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        session()->flush();
        $this->redirect('/');
    }

    public function render()
    {
        $users = User::find(session()->get('user_id'));
        $this->user_name = $users->name;
        $this->user_email = $users->email;
        $this->user_role = $users->role;
        return view('livewire.sidebar', compact('users'));
    }
}
