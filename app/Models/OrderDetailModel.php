<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetailModel extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'discount', 'subtotal'];

    public $timestamps = false;

    public function order() {
        return $this->belongsTo(OrderModel::class, 'order_id');
    }

    public function product() {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}