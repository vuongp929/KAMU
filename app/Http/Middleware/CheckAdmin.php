<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isAdmin()) { // Giả sử có hàm isAdmin() trong User model
            return $next($request);
        }
        return redirect('/')->with('error', 'Bạn không có quyền truy cập vào trang này.');
    }
}