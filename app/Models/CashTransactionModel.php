<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransactionModel extends Model{
    protected $table = 'cash_transactions';

    protected $fillable = [
        'type',
        'amount',
        'previous_balance',
        'balance',
        'description',
        'order_id',
    ];

    public function order(){
        return $this->belongsTo(OrderModel::class, 'order_id');
    }
}