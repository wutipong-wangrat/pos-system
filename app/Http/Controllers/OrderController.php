<?php

namespace App\Http\Controllers;

class OrderController extends Controller
{
    public function order()
    {
        return view('order');
    }

    // public function checkout(){
    //     if (!session()->has('cart')) {
    //         return redirect()->route('order')->with('error', 'Your cart is empty.');
    //     }

    //     return view('order.checkout');
    // }
}