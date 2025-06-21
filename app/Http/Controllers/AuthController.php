<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admins.auth.login');
    }

    public function postLogin(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required' => 'Email không được để trống.',
                'email.email' => 'Email không đúng định dạng.',
                'password.required' => 'Mật khẩu không được để trống.',
            ]
        );
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->back()->withErrors(['email' => 'Tài khoản mật khẩu không đúng.'])->withInput();
    }

    public function logOut($id)
    {
        $user = Auth::user();
        if ($user->id != $id) {
            return redirect()->back()->withErrors(['error' => 'You cannot log out this user']);
        }
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
