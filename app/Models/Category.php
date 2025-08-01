<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Định nghĩa bảng mà model này tương ứng
    protected $table = 'categories';

    // Các thuộc tính có thể gán (fillable)
    protected $fillable = [
        'name', 
        'slug' ,
        'parent_id',
        'image',
        'statu'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function activeChildren()
    {
        return $this->hasMany(Category::class, 'parent_id')->where('statu', 1);
    }


    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category', 'category_id', 'product_id')
                    ->withTimestamps(); // Thêm thông tin thời gian nếu cần
    }
    
}
