<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    // public function postLogin(Request $request)
    // {
    //     $request->validate(
    //         [
    //             'email' => 'required|email',
    //             'password' => 'required',
    //         ],
    //         [
    //             'email.required' => 'Email không được để trống.',
    //             'email.email' => 'Email không đúng định dạng.',
    //             'password.required' => 'Mật khẩu không được để trống.',
    //         ]
    //     );
    //     if (Auth::attempt($request->only('email', 'password'))) {
    //         return redirect()->route('admin.dashboard');
    //     }
    //     return redirect()->back()->withErrors(['email' => 'Tài khoản mật khẩu không đúng.'])->withInput();
    // }
    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Mật khẩu không được để trống.',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        // So sánh mật khẩu thuần (KHÔNG mã hóa)
        if ($user && $user->password === $request->password) {
            Auth::login($user); // Không dùng attempt nữa
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()
            ->withErrors(['email' => 'Tài khoản hoặc mật khẩu không đúng.'])
            ->withInput();
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

    public function listUser()
    {
        $users = User::with('roles')->where('id', '<>', Auth::user()->id)->paginate(10);
        return view('admins.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admins.users.create');
    }

    public function storeUser()
    {
        $data = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,customer',
        ]);

        // Kiểm tra trùng email
        if (User::where('email', $data['email'])->exists()) {
            return redirect()->back()
                ->withErrors(['email' => 'Email đã tồn tại.'])
                ->withInput();
        }

        $data['password'] = bcrypt($data['password']); 

        User::create($data);
        // Tạo vai trò cho người dùng mới
        $user = User::where('email', $data['email'])->first();
        $user->roles()->create(['role' => $data['role']]);

        return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công !');
    }

    public function editUser($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('admins.users.edit', compact('user'));
    }
    public function updateUser()
    {

        $data = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,customer',
        ]);
        $user = User::findOrFail(request('id'));
        // Kiểm tra trùng email
        if (User::where('email', $data['email'])->where('id', '<>', $user->id)->exists()) {
            return redirect()->back()
                ->withErrors(['email' => 'Email đã tồn tại.'])
                ->withInput();
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();
        // Cập nhật vai trò
        $user->roles()->delete(); 
        $user->roles()->create(['role' => $data['role']]); 

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công !');
    }

    public function deleteUser()
    {
        $user = User::findOrFail(request('id'));
        if (Auth::user()->id == $user->id) {
            return redirect()->back()->withErrors(['error' => 'Bạn không thể xóa tài khoản của chính mình.']);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công !');
    }
}
