<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
        public function giaoHang()
    {
        return view('clients.pages.giao-hang');
    }
        public function goiQua()
    {
        return view('clients.pages.dich-vu-goi-qua');
    }
        public function giatGau()
    {
        return view('clients.pages.cach-giat-gau-bong');
    }
        public function doiTra()
    {
        return view('clients.pages.chinh-sach-doi-tra');
    }

    // Các method cho dịch vụ
    public function guide()
    {
        return view('clients.services.guide');
    }

    public function washing()
    {
        return view('clients.services.washing');
    }

    public function giftWrap()
    {
        return view('clients.services.gift-wrap');
    }

    public function freeCard()
    {
        return view('clients.services.free-card');
    }
}
