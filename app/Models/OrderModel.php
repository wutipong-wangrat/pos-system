<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $fillable = ['user_id', 'total_amount', 'tax_amount', 'subtotal', 'status',
        'payment_status', 'payment_method', 'cash_received', 'change_amount', 'delivery_address', 'delivery_status'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public $timestamps = true;

    public function orderDetails()
    {
        return $this->hasMany(OrderDetailModel::class, 'order_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
