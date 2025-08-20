<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép tất cả
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:categories,name',
            'slug' => 'nullable|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'statu' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'slug.unique' => 'Slug đã tồn tại.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
            'statu.in' => 'Trạng thái không hợp lệ.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh chỉ chấp nhận định dạng jpg, jpeg, png, gif.',
            'image.max' => 'Hình ảnh không được vượt quá 2MB.',
        ];
    }
}
