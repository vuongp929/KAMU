<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // $query = Category::with('parent');
        $query = Category::with('parent')->whereNull('parent_id'); 

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('statu')) {
            $query->where('statu', (int) $request->statu); // Ép kiểu INT để chắc chắn
        }

        // Phân trang
        $listCategory = $query->paginate(50)->appends($request->all()); // giữ lại các filter khi phân trang

        return view('admins.categories.index', compact('listCategory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       $listCategory = Category::whereNull('parent_id')->get(); // Chỉ lấy danh mục cha
    return view('admins.categories.create', compact('listCategory'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreCategoryRequest  $request)
{
    $request->validate([
        'name' => 'required|unique:categories,name',
        'slug' => 'nullable|unique:categories,slug',
        'parent_id' => 'nullable|exists:categories,id',
    ]);

    $slug = $request->slug ?? Str::slug($request->name);
    $originalSlug = $slug;
    $i = 1;
    while (Category::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $i++;
    }

    // Nếu có parent_id => là danh mục con => mặc định ẩn (statu = 0)
    $statu = $request->parent_id ? 0 : ($request->statu ?? 1);

    Category::create([
        'name' => $request->name,
        'slug' => $slug,
        'statu' => $statu,
        'parent_id' => $request->parent_id,
        'image' => $request->hasFile('image')
            ? $request->file('image')->store('uploads/categorys', 'public')
            : null,
    ]);

    return redirect()->route('admins.categories.index')->with('success', 'Thêm danh mục thành công');
}

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $Category = Category::findOrFail($id);

        // Chỉ lấy danh mục cha (parent_id = null) và khác với chính nó
        $categories = Category::whereNull('parent_id')
                            ->where('id', '!=', $id)
                            ->get();

        return view('admins.categories.edit', compact('Category', 'categories'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest  $request, string $id)
    {
         if ($request->isMethod('PUT')) {
            $params = $request->except('_token', '_method');
            $Category = Category::findOrFail($id);
            
            if ($request->hasFile('image')) {
                if (!isset($params['parent_id'])) {
                    $params['parent_id'] = null; // Gán null nếu không gửi lên (khi bị disable)
                }

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
    
            return redirect()->route('admins.categories.index')->with('success', 'Cập nhật danh mục thành công');
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
        return redirect()->route('admins.categories.index')->with('success', 'Xoá danh mục thành công');

    }
}
