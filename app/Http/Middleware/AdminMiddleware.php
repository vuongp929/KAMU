<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response; // Sử dụng Response của Symfony

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // // Sử dụng hàm isAdmin() đã có trong model User để kiểm tra
        // if (Auth::check() && Auth::user()->isAdmin()) {
        //     // Nếu là admin, cho phép request đi tiếp.
        //     return $next($request);
        // }

        // // Nếu không phải là admin, trả về trang lỗi 403 (Forbidden)
        // // Đây là cách làm chuẩn hơn là redirect về trang chủ.
        // abort(403, 'BẠN KHÔNG CÓ QUYỀN TRUY CẬP TRANG NÀY.');
         if (!auth()->check() || auth()->user()->role !== 'admin') {
        abort(403, 'Bạn không có quyền truy cập trang này.');
    }

    return $next($request);
    }
}