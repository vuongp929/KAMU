<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    /**
     * Hiển thị danh sách tất cả các thuộc tính.
     */
    public function index()
    {
        $attributes = Attribute::with('values')->latest()->get();
        return view('admins.attributes.index', compact('attributes'));
    }

    /**
     * Hiển thị form để tạo một thuộc tính mới.
     */
    public function create()
    {
        return view('admins.attributes.create');
    }

    /**
     * Lưu thuộc tính mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
            'values' => 'required|string', // Nhận các giá trị dưới dạng một chuỗi, cách nhau bởi dấu phẩy
        ]);

        DB::beginTransaction();
        try {
            // 1. Tạo thuộc tính cha
            $attribute = Attribute::create(['name' => $validated['name']]);

            // 2. Xử lý và tạo các giá trị con
            $values = array_map('trim', explode(',', $validated['values'])); // Tách chuỗi thành mảng và xóa khoảng trắng thừa
            $values = array_filter($values); // Loại bỏ các giá trị rỗng

            foreach ($values as $value) {
                $attribute->values()->create(['value' => $value]);
            }

            DB::commit();
            return redirect()->route('admins.attributes.index')->with('success', 'Thêm thuộc tính thành công!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị form để chỉnh sửa một thuộc tính.
     */
    public function edit(Attribute $attribute)
    {
        // Tải các giá trị để hiển thị trong form
        $attribute->load('values');
        return view('admins.attributes.edit', compact('attribute'));
    }

    /**
     * Cập nhật thuộc tính trong database.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
            'values' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Cập nhật tên thuộc tính cha
            $attribute->update(['name' => $validated['name']]);

            // 2. Đồng bộ hóa các giá trị con (xóa cũ, tạo mới)
            $attribute->values()->delete(); // Xóa tất cả các giá trị cũ

            $values = array_map('trim', explode(',', $validated['values']));
            $values = array_filter($values);

            foreach ($values as $value) {
                $attribute->values()->create(['value' => $value]);
            }

            DB::commit();
            return redirect()->route('admins.attributes.index')->with('success', 'Cập nhật thuộc tính thành công!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa một thuộc tính và tất cả các giá trị của nó.
     */
    public function destroy(Attribute $attribute)
    {
        // Do đã thiết lập onDelete('cascade') trong migration,
        // khi xóa thuộc tính cha, các giá trị con cũng sẽ tự động bị xóa.
        $attribute->delete();
        return redirect()->route('admins.attributes.index')->with('success', 'Xóa thuộc tính thành công!');
    }
}