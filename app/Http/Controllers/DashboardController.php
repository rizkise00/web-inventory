<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalStockIn = StockIn::sum('quantity');
        $totalStockOut = StockOut::sum('quantity');

        return view('dashboard', compact('totalUsers', 'totalStockIn', 'totalStockOut'));
    }
}
