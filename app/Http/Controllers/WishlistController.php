<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\SanPham;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())->with('product')->get();
        return view('clients.wishlist.index', compact('wishlists'));
    }

    public function addWishlist(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thêm sản phẩm vào wishlist.');
        }

        $product_id = $request->product_id;
        $user_id = Auth::id();

        $wishlist = Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->first();

        if ($wishlist) {
            return back()->with('info', 'Sản phẩm này đã có trong wishlist.');
        }

        Wishlist::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
        ]);

        return back()->with('success', 'Đã thêm sản phẩm vào wishlist.');
    }

    public function removeWishlist($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('id', $id)->first();
        
        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Đã xóa sản phẩm khỏi wishlist.');
        }

        return back()->with('error', 'Không tìm thấy sản phẩm trong wishlist.');
    }
}

