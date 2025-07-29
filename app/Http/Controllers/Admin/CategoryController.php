<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $listCategory = Category::orderByDesc('trang_thai')->get();
        $listCategory = Category::all();
        return view('admins.categories.index', compact( 'listCategory'));   

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    if ($request->isMethod('POST')) {
        $params = $request->except('_token');

        // Xử lý ảnh
        if ($request->hasFile('image')) {
            $filepath = $request->file('image')->store('uploads/categorys', 'public');
        } else {
            $filepath = null;
        }

        // Gán slug từ name
        $params['slug'] = Str::slug($request->input('name'));
        $params['image'] = $filepath;

        Category::create($params);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công');
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $Category = Category::findOrFail($id);
        return view('admins.categories.edit', compact('Category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         if ($request->isMethod('PUT')) {
            $params = $request->except('_token', '_method');
            $Category = Category::findOrFail($id);
            
            if ($request->hasFile('image')) {
                // Xóa hình ảnh cũ nếu tồn tại
                if ($Category->image && Storage::disk('public')->exists($Category->image)) {
                    Storage::disk('public')->delete($Category->image);
                }
                
                // Lưu hình ảnh mới
                $file = $request->file('image')->store('uploads/Categorys', 'public');
                $params['image'] = $file;
            } 
    
            // Cập nhật dữ liệu
            $Category->update($params);
    
            return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Category = Category::findOrFail($id);

        if($Category){
            $Category->delete();
            if ($Category->image && Storage::disk('public')->exists($Category->image)) {
                Storage::disk('public')->delete($Category->image);
            }
        }
        return redirect()->route('admin.categories.index')->with('success', 'Xoá danh mục thành công');

    }
}
