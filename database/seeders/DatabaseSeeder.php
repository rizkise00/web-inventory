<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Maintenance;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create 15 Users
        $users = [];
        
        $users[] = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_approved' => true,
        ]);

        $users[] = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_approved' => true,
        ]);

        for ($i = 3; $i <= 15; $i++) {
            $users[] = User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
                'role' => ($i % 3 == 0) ? 'manager' : 'admin',
                'is_approved' => true,
            ]);
        }

        // 2. Create 15 Items
        $itemNames = [
            'Laptop Dell XPS 13', 'Mouse Logitech MX Master', 'Keyboard Keychron K2',
            'Monitor LG 27 Inch 4K', 'MacBook Pro M2 14 Inch', 'IKEA Markus Chair',
            'Standing Desk Omnidesk', 'USB-C Hub Anker 7-in-1', 'Webcam Logitech Brio',
            'Headset Sony WH-1000XM5', 'Microphone Blue Yeti', 'External HDD WD 2TB',
            'SSD Samsung T7 1TB', 'iPad Air 5th Gen', 'Apple Pencil Gen 2'
        ];

        $items = [];
        foreach ($itemNames as $name) {
            $items[] = Item::create([
                'name' => $name,
                'stock' => 0, // Let Stock In events handle the stock
                'description' => "High quality $name for office and productivity.",
            ]);
        }

        // 3. Create 15 Stock In records
        // We will loop through the 15 items and create 1 stock in for each
        foreach ($items as $index => $item) {
            $quantityIn = rand(50, 100); // 50 to 100 units each
            StockIn::create([
                'item_id' => $item->id,
                'quantity' => $quantityIn,
                'notes' => "Initial inventory batch for {$item->name}",
                'user_id' => $users[array_rand($users)]->id,
                'created_at' => Carbon::now()->subDays(rand(10, 30)),
            ]);
        }

        // 4. Create 15 Stock Out records
        // We will loop through the 15 items and create 1 stock out for each
        foreach ($items as $index => $item) {
            $quantityOut = rand(5, 20); // Safe deduction, well below the 50-100 added
            StockOut::create([
                'item_id' => $item->id,
                'quantity' => $quantityOut,
                'notes' => "Distributed to staff members",
                'user_id' => $users[array_rand($users)]->id,
                'created_at' => Carbon::now()->subDays(rand(1, 9)),
            ]);
        }

        // 5. Create 15 Maintenance records
        $statuses = ['Pending', 'In Progress', 'Completed'];
        foreach ($items as $index => $item) {
            Maintenance::create([
                'item_name' => $item->name,
                'date' => Carbon::now()->subDays(rand(1, 15)),
                'status' => $statuses[array_rand($statuses)],
                'description' => "Routine checkup and cleaning for {$item->name}",
            ]);
        }
    }
}
