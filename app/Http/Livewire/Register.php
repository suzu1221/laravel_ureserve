<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    public $name;
    public $email;
    public $password;

    // バリデーション設定
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8', 
    ];
    
    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function register()
    {
        // バリデーションチェック実施
        $this->validate();

        // dd($this);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        session()->flash('message','登録OKです');

        // laravel8.x以前では「redirect()」という書き方だったが、
        // 9.x以降では「to_route()」で記載できる
        return to_route('livewire-test.index');
    }

    public function render()
    {
        return view('livewire.register');
    }
}
