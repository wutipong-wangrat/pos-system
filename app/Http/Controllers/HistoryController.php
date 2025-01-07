<?php

namespace App\Http\Controllers;

use App\Models\OrderModel;

class HistoryController extends Controller
{
    public function history()
    {
        return view('history');
    }

}