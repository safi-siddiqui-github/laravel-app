<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function login_post(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users'],
            'password' => ['required', 'string', 'min:5'],
            'remember' => ['in:on'],
        ]);

        if (Auth::attempt($request->only(['email', 'password']), $request->has('remember') ? true : false)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home.index'));
        }

        return back()->withErrors([
            'password' => 'Password incorrect.',
        ])->onlyInput('email');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function register_post(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'unique:users', 'max:100'],
            'username' => ['required', 'string', 'unique:users', 'max:100'],
            'password' => ['required', 'string', 'min:5', 'max:100'],
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->password = $request->input('password');
        $user->save();

        return to_route('home.index');
    }

    public function logout_post(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home.index');
    }
}
