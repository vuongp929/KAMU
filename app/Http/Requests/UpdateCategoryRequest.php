<?php

// app/Http/Requests/UpdateCategoryRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category') ?? $this->route('id'); // tùy route đặt sao

        return [
            'name' => ['required', Rule::unique('categories', 'name')->ignore($categoryId)],
            'slug' => ['nullable', Rule::unique('categories', 'slug')->ignore($categoryId)],
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
            'image.mimes' => 'Chỉ cho phép ảnh jpg, jpeg, png, gif.',
            'image.max' => 'Hình ảnh tối đa 2MB.',
        ];
    }
}
