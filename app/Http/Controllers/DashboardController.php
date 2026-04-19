<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use App\Models\Item;
use App\Models\Maintenance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalStockIn = StockIn::sum('quantity');
        $totalStockOut = StockOut::sum('quantity');
        $totalItems = Item::count();
        $totalMaintenances = Maintenance::count();

        return view('dashboard', compact('totalUsers', 'totalStockIn', 'totalStockOut', 'totalItems', 'totalMaintenances'));
    }
}
