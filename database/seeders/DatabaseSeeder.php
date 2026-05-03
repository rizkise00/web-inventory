<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Maintenance;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Category;
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

        // 2. Create Categories
        $categories = [];
        $categoryNames = ['Electronics', 'Furniture', 'Office Supplies', 'Computing', 'Accessories'];
        foreach ($categoryNames as $name) {
            $categories[] = Category::create(['name' => $name]);
        }

        // 3. Create 15 Items (Products)
        $itemData = [
            ['name' => 'Laptop Dell XPS 13', 'price' => 15000000],
            ['name' => 'Mouse Logitech MX Master', 'price' => 1200000],
            ['name' => 'Keyboard Keychron K2', 'price' => 1500000],
            ['name' => 'Monitor LG 27 Inch 4K', 'price' => 5000000],
            ['name' => 'MacBook Pro M2 14 Inch', 'price' => 28000000],
            ['name' => 'IKEA Markus Chair', 'price' => 2500000],
            ['name' => 'Standing Desk Omnidesk', 'price' => 7000000],
            ['name' => 'USB-C Hub Anker 7-in-1', 'price' => 800000],
            ['name' => 'Webcam Logitech Brio', 'price' => 2500000],
            ['name' => 'Headset Sony WH-1000XM5', 'price' => 5000000],
            ['name' => 'Microphone Blue Yeti', 'price' => 2000000],
            ['name' => 'External HDD WD 2TB', 'price' => 1000000],
            ['name' => 'SSD Samsung T7 1TB', 'price' => 1800000],
            ['name' => 'iPad Air 5th Gen', 'price' => 9000000],
            ['name' => 'Apple Pencil Gen 2', 'price' => 2000000]
        ];

        $items = [];
        foreach ($itemData as $data) {
            $items[] = Item::create([
                'name' => $data['name'],
                'category_id' => $categories[array_rand($categories)]->id,
                'price' => $data['price'],
                'stock' => 0,
                'description' => "High quality {$data['name']} for office and productivity.",
            ]);
        }

        // 4. Create 15 Stock In records
        foreach ($items as $item) {
            $quantityIn = rand(50, 100);
            $unitPrice = $item->price;
            StockIn::create([
                'item_id' => $item->id,
                'quantity' => $quantityIn,
                'unit_price' => $unitPrice,
                'total_price' => $unitPrice * $quantityIn,
                'notes' => "Initial inventory batch for {$item->name}",
                'user_id' => $users[array_rand($users)]->id,
                'created_at' => Carbon::now()->subDays(rand(10, 30)),
            ]);
        }

        // 5. Create 15 Stock Out records
        foreach ($items as $item) {
            $quantityOut = rand(5, 20);
            $unitPrice = $item->price;
            StockOut::create([
                'item_id' => $item->id,
                'quantity' => $quantityOut,
                'unit_price' => $unitPrice,
                'total_price' => $unitPrice * $quantityOut,
                'notes' => "Distributed to staff members",
                'user_id' => $users[array_rand($users)]->id,
                'created_at' => Carbon::now()->subDays(rand(1, 9)),
            ]);
        }

        // 6. Create 15 Maintenance records
        $statuses = ['Pending', 'In Progress', 'Completed'];
        foreach ($items as $item) {
            Maintenance::create([
                'item_id' => $item->id,
                'quantity' => rand(1, 3),
                'date' => Carbon::now()->subDays(rand(1, 15)),
                'status' => $statuses[array_rand($statuses)],
                'description' => "Routine maintenance check for {$item->name}.",
                'user_id' => $users[array_rand($users)]->id,
            ]);
        }
    }
}
