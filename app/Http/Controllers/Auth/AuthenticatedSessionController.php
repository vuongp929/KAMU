<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(route('admin.dashboard', absolute: false));
    // }
    public function store(Request $request): RedirectResponse
{
    // Validate đầu vào đơn giản
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Tìm user theo email
    $user = \App\Models\User::where('email', $request->email)->first();

    // So sánh mật khẩu thường (plain text)
    if (!$user || $request->password !== $user->password) {
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ]);
    }

    // Đăng nhập
    Auth::login($user);

    // Khởi tạo session mới
    $request->session()->regenerate();

    // Chuyển hướng sau khi đăng nhập thành công
    return redirect()->intended(route('admin.dashboard'));
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}