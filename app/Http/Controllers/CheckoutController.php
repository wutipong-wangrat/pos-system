<?php

namespace App\Http\Controllers;

class CheckoutController extends Controller
{
    public function checkout()
    {
        if (!session()->has('cart')) {
            return redirect()->route('order')->with('error', 'Your cart is empty');
        }

        return view('checkout');
    }
}
