<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Maintenance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DefaultSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('TRUNCATE TABLE maintenances, stock_outs, stock_ins, items, categories, users RESTART IDENTITY CASCADE');

        User::create([
            'name'        => 'Manager User',
            'email'       => 'manager@example.com',
            'password'    => Hash::make('password'),
            'role'        => 'manager',
            'is_approved' => true,
        ]);

        User::create([
            'name'        => 'Admin User',
            'email'       => 'admin@example.com',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'is_approved' => true,
        ]);
    }
}
