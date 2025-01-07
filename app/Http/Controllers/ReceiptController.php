<?php

namespace App\Http\Controllers;

use App\Models\OrderModel;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function print(OrderModel $order)
    {
        // Load the order with its relationships
        $order->load(['orderDetails.product', 'user']);

        // Format dates for Thai locale
        $thaiMonths = [
            '1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม',
            '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน',
            '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน',
            '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม',
        ];

        $date = $order->created_at;
        $thaiDate = $date->format('d') . ' ' .
            $thaiMonths[$date->format('n')] . ' ' .
            ($date->format('Y') + 543);

        // Calculate totals
        $subtotal = $order->orderDetails->sum('subtotal');
        $tax = $order->tax_amount;
        $total = $order->total_amount;

        return view('print', compact(
            'order',
            'thaiDate',
            'subtotal',
            'tax',
            'total'
        ));
    }
}
