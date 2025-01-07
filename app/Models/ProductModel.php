<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $fillable = ['name', 'description', 'price', 'quantity', 'status' , 'category_id', 'img'];

    public $timestamps = false;

    public function category(){
        return $this->belongsTo(CategoryModel::class, 'category_id', 'id');
    }

    // public function getCategory(){
    //     return $this->categories->where('category_id', $this->categoryId)->get();
    // }

    public function getStatus(){
        $product = ProductModel::where('id', $this->id)->first();
        if ($product->status == 'available'){
            return 'มีสินค้า';
        } else {
            return 'ไม่มีสินค้า';
        }
    }
}