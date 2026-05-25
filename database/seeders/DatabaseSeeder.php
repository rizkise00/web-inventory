<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Maintenance;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ─── Users (15) ───────────────────────────────────────────────────
        $userData = [
            ['name' => 'Manager User',  'email' => 'manager@example.com', 'role' => 'manager', 'is_approved' => true,  'days' => 90],
            ['name' => 'Admin User',    'email' => 'admin@example.com',   'role' => 'admin',   'is_approved' => true,  'days' => 88],
            ['name' => 'Budi Santoso',  'email' => 'budi@example.com',    'role' => 'admin',   'is_approved' => true,  'days' => 75],
            ['name' => 'Siti Rahayu',   'email' => 'siti@example.com',    'role' => 'manager', 'is_approved' => true,  'days' => 60],
            ['name' => 'Andi Wijaya',   'email' => 'andi@example.com',    'role' => 'admin',   'is_approved' => true,  'days' => 50],
            ['name' => 'Dewi Kusuma',   'email' => 'dewi@example.com',    'role' => 'admin',   'is_approved' => true,  'days' => 40],
            ['name' => 'Reza Pratama',  'email' => 'reza@example.com',    'role' => 'manager', 'is_approved' => true,  'days' => 30],
            ['name' => 'Nina Lestari',  'email' => 'nina@example.com',    'role' => 'admin',   'is_approved' => true,  'days' => 20],
            ['name' => 'Hendra Putra',  'email' => 'hendra@example.com',  'role' => 'admin',   'is_approved' => false, 'days' => 10],
            ['name' => 'Yuni Astuti',   'email' => 'yuni@example.com',    'role' => 'admin',   'is_approved' => false, 'days' => 8],
            ['name' => 'Fajar Hidayat', 'email' => 'fajar@example.com',   'role' => 'manager', 'is_approved' => false, 'days' => 6],
            ['name' => 'Maya Sari',     'email' => 'maya@example.com',    'role' => 'admin',   'is_approved' => false, 'days' => 4],
            ['name' => 'Doni Setiawan', 'email' => 'doni@example.com',    'role' => 'admin',   'is_approved' => true,  'days' => 15],
            ['name' => 'Rina Wati',     'email' => 'rina@example.com',    'role' => 'manager', 'is_approved' => true,  'days' => 25],
            ['name' => 'Bagas Nugroho', 'email' => 'bagas@example.com',   'role' => 'admin',   'is_approved' => false, 'days' => 2],
        ];

        $users = [];
        foreach ($userData as $data) {
            $user = User::create([
                'name'        => $data['name'],
                'email'       => $data['email'],
                'password'    => Hash::make('password'),
                'role'        => $data['role'],
                'is_approved' => $data['is_approved'],
            ]);
            $ts = $now->copy()->subDays($data['days'])->toDateTimeString();
            DB::table('users')->where('id', $user->id)->update(['created_at' => $ts, 'updated_at' => $ts]);
            $users[] = $user;
        }

        // ─── Categories (6) ───────────────────────────────────────────────
        $categoryData = [
            ['name' => 'Electronics',    'days' => 80],
            ['name' => 'Furniture',      'days' => 65],
            ['name' => 'Office Supplies','days' => 50],
            ['name' => 'Computing',      'days' => 35],
            ['name' => 'Accessories',    'days' => 20],
            ['name' => 'Networking',     'days' => 10],
        ];

        $categories = [];
        foreach ($categoryData as $data) {
            $cat = Category::create(['name' => $data['name']]);
            $ts = $now->copy()->subDays($data['days'])->toDateTimeString();
            DB::table('categories')->where('id', $cat->id)->update(['created_at' => $ts, 'updated_at' => $ts]);
            $categories[] = $cat;
        }

        // ─── Items (15) ───────────────────────────────────────────────────
        // cat indices: 0=Electronics,1=Furniture,2=OfficeSupplies,3=Computing,4=Accessories,5=Networking
        $itemData = [
            ['name' => 'Laptop Dell XPS 13',      'price' => 15000000, 'cat' => 0, 'days' => 75],
            ['name' => 'Mouse Logitech MX Master', 'price' => 1200000,  'cat' => 4, 'days' => 72],
            ['name' => 'Keyboard Keychron K2',     'price' => 1500000,  'cat' => 4, 'days' => 70],
            ['name' => 'Monitor LG 27 Inch 4K',    'price' => 5000000,  'cat' => 0, 'days' => 68],
            ['name' => 'MacBook Pro M2 14 Inch',   'price' => 28000000, 'cat' => 3, 'days' => 65],
            ['name' => 'IKEA Markus Chair',         'price' => 2500000,  'cat' => 1, 'days' => 60],
            ['name' => 'Standing Desk Omnidesk',    'price' => 7000000,  'cat' => 1, 'days' => 55],
            ['name' => 'USB-C Hub Anker 7-in-1',   'price' => 800000,   'cat' => 4, 'days' => 50],
            ['name' => 'Webcam Logitech Brio',      'price' => 2500000,  'cat' => 0, 'days' => 48],
            ['name' => 'Headset Sony WH-1000XM5',  'price' => 5000000,  'cat' => 0, 'days' => 45],
            ['name' => 'Microphone Blue Yeti',      'price' => 2000000,  'cat' => 0, 'days' => 40],
            ['name' => 'External HDD WD 2TB',       'price' => 1000000,  'cat' => 3, 'days' => 35],
            ['name' => 'SSD Samsung T7 1TB',        'price' => 1800000,  'cat' => 3, 'days' => 28],
            ['name' => 'iPad Air 5th Gen',          'price' => 9000000,  'cat' => 3, 'days' => 20],
            ['name' => 'Apple Pencil Gen 2',        'price' => 2000000,  'cat' => 4, 'days' => 14],
        ];

        $items = [];
        foreach ($itemData as $data) {
            $item = Item::create([
                'name'        => $data['name'],
                'category_id' => $categories[$data['cat']]->id,
                'price'       => $data['price'],
                'stock'       => 0,
                'description' => "High quality {$data['name']} for office and productivity.",
            ]);
            $ts = $now->copy()->subDays($data['days'])->toDateTimeString();
            DB::table('items')->where('id', $item->id)->update(['created_at' => $ts, 'updated_at' => $ts]);
            $items[] = $item;
        }

        // ─── Stock In (25 records) ────────────────────────────────────────
        // Batch 1: initial stock 55–85 days ago | Batch 2: replenishment 12–35 days ago
        $stockInData = [
            // Batch 1: initial inventory
            ['item' => 0,  'qty' => 80,  'days' => 85, 'user' => 0, 'note' => 'Initial inventory batch'],
            ['item' => 1,  'qty' => 100, 'days' => 82, 'user' => 1, 'note' => 'Initial inventory batch'],
            ['item' => 2,  'qty' => 90,  'days' => 80, 'user' => 2, 'note' => 'Initial inventory batch'],
            ['item' => 3,  'qty' => 75,  'days' => 78, 'user' => 3, 'note' => 'Initial inventory batch'],
            ['item' => 4,  'qty' => 60,  'days' => 75, 'user' => 4, 'note' => 'Initial inventory batch'],
            ['item' => 5,  'qty' => 80,  'days' => 72, 'user' => 0, 'note' => 'Initial inventory batch'],
            ['item' => 6,  'qty' => 70,  'days' => 70, 'user' => 1, 'note' => 'Initial inventory batch'],
            ['item' => 7,  'qty' => 100, 'days' => 68, 'user' => 5, 'note' => 'Initial inventory batch'],
            ['item' => 8,  'qty' => 80,  'days' => 65, 'user' => 6, 'note' => 'Initial inventory batch'],
            ['item' => 9,  'qty' => 70,  'days' => 60, 'user' => 0, 'note' => 'Initial inventory batch'],
            ['item' => 10, 'qty' => 90,  'days' => 58, 'user' => 1, 'note' => 'Initial inventory batch'],
            ['item' => 11, 'qty' => 80,  'days' => 55, 'user' => 7, 'note' => 'Initial inventory batch'],
            ['item' => 12, 'qty' => 75,  'days' => 50, 'user' => 2, 'note' => 'Initial inventory batch'],
            ['item' => 13, 'qty' => 60,  'days' => 45, 'user' => 3, 'note' => 'Initial inventory batch'],
            ['item' => 14, 'qty' => 85,  'days' => 40, 'user' => 0, 'note' => 'Initial inventory batch'],
            // Batch 2: replenishment
            ['item' => 0,  'qty' => 30, 'days' => 35, 'user' => 1, 'note' => 'Replenishment stock'],
            ['item' => 1,  'qty' => 40, 'days' => 32, 'user' => 2, 'note' => 'Replenishment stock'],
            ['item' => 3,  'qty' => 25, 'days' => 30, 'user' => 0, 'note' => 'Replenishment stock'],
            ['item' => 5,  'qty' => 20, 'days' => 28, 'user' => 3, 'note' => 'Replenishment stock'],
            ['item' => 7,  'qty' => 35, 'days' => 25, 'user' => 4, 'note' => 'Replenishment stock'],
            ['item' => 9,  'qty' => 30, 'days' => 22, 'user' => 0, 'note' => 'Replenishment stock'],
            ['item' => 11, 'qty' => 20, 'days' => 20, 'user' => 5, 'note' => 'Replenishment stock'],
            ['item' => 12, 'qty' => 25, 'days' => 18, 'user' => 1, 'note' => 'Replenishment stock'],
            ['item' => 13, 'qty' => 20, 'days' => 15, 'user' => 6, 'note' => 'Replenishment stock'],
            ['item' => 14, 'qty' => 30, 'days' => 12, 'user' => 0, 'note' => 'Replenishment stock'],
        ];

        foreach ($stockInData as $data) {
            $item      = $items[$data['item']];
            $unitPrice = $item->price;
            $si = StockIn::create([
                'item_id'     => $item->id,
                'quantity'    => $data['qty'],
                'unit_price'  => $unitPrice,
                'total_price' => $unitPrice * $data['qty'],
                'notes'       => $data['note'],
                'user_id'     => $users[$data['user']]->id,
            ]);
            $ts = $now->copy()->subDays($data['days'])->toDateTimeString();
            DB::table('stock_ins')->where('id', $si->id)->update(['created_at' => $ts, 'updated_at' => $ts]);
        }

        // ─── Stock Out (20 records) ───────────────────────────────────────
        // 10 Consumed + 10 Damaged, spread over 5–60 days ago
        $stockOutData = [
            ['item' => 0,  'qty' => 10, 'status' => 'Consumed', 'days' => 60, 'user' => 1],
            ['item' => 1,  'qty' => 15, 'status' => 'Damaged',  'days' => 58, 'user' => 2],
            ['item' => 2,  'qty' => 8,  'status' => 'Consumed', 'days' => 55, 'user' => 3],
            ['item' => 3,  'qty' => 12, 'status' => 'Damaged',  'days' => 50, 'user' => 4],
            ['item' => 4,  'qty' => 5,  'status' => 'Consumed', 'days' => 48, 'user' => 0],
            ['item' => 5,  'qty' => 10, 'status' => 'Consumed', 'days' => 45, 'user' => 1],
            ['item' => 6,  'qty' => 7,  'status' => 'Damaged',  'days' => 42, 'user' => 5],
            ['item' => 7,  'qty' => 20, 'status' => 'Consumed', 'days' => 40, 'user' => 0],
            ['item' => 8,  'qty' => 10, 'status' => 'Damaged',  'days' => 38, 'user' => 6],
            ['item' => 9,  'qty' => 12, 'status' => 'Consumed', 'days' => 35, 'user' => 1],
            ['item' => 10, 'qty' => 8,  'status' => 'Damaged',  'days' => 30, 'user' => 2],
            ['item' => 11, 'qty' => 10, 'status' => 'Consumed', 'days' => 28, 'user' => 3],
            ['item' => 12, 'qty' => 6,  'status' => 'Consumed', 'days' => 25, 'user' => 0],
            ['item' => 13, 'qty' => 8,  'status' => 'Damaged',  'days' => 22, 'user' => 4],
            ['item' => 14, 'qty' => 15, 'status' => 'Consumed', 'days' => 20, 'user' => 1],
            ['item' => 0,  'qty' => 5,  'status' => 'Damaged',  'days' => 18, 'user' => 5],
            ['item' => 2,  'qty' => 10, 'status' => 'Consumed', 'days' => 15, 'user' => 0],
            ['item' => 4,  'qty' => 3,  'status' => 'Consumed', 'days' => 12, 'user' => 6],
            ['item' => 7,  'qty' => 8,  'status' => 'Damaged',  'days' => 8,  'user' => 1],
            ['item' => 10, 'qty' => 5,  'status' => 'Consumed', 'days' => 5,  'user' => 2],
        ];

        foreach ($stockOutData as $data) {
            $item      = $items[$data['item']];
            $unitPrice = $item->price;
            $so = StockOut::create([
                'item_id'     => $item->id,
                'quantity'    => $data['qty'],
                'status'      => $data['status'],
                'unit_price'  => $unitPrice,
                'total_price' => $unitPrice * $data['qty'],
                'notes'       => $data['status'] === 'Consumed' ? 'Distributed to staff members' : 'Damaged items removed from inventory',
                'user_id'     => $users[$data['user']]->id,
            ]);
            $ts = $now->copy()->subDays($data['days'])->toDateTimeString();
            DB::table('stock_outs')->where('id', $so->id)->update(['created_at' => $ts, 'updated_at' => $ts]);
        }

        // ─── Maintenance (15 records) ─────────────────────────────────────
        // 5 Pending + 5 In Progress + 5 Completed, spread over 3–55 days ago
        $maintenanceData = [
            // Pending
            ['item' => 0,  'qty' => 2, 'status' => 'Pending',     'days' => 15, 'user' => 0],
            ['item' => 3,  'qty' => 1, 'status' => 'Pending',     'days' => 10, 'user' => 1],
            ['item' => 6,  'qty' => 2, 'status' => 'Pending',     'days' => 8,  'user' => 3],
            ['item' => 9,  'qty' => 1, 'status' => 'Pending',     'days' => 5,  'user' => 0],
            ['item' => 12, 'qty' => 2, 'status' => 'Pending',     'days' => 3,  'user' => 4],
            // In Progress
            ['item' => 1,  'qty' => 1, 'status' => 'In Progress', 'days' => 20, 'user' => 2],
            ['item' => 4,  'qty' => 2, 'status' => 'In Progress', 'days' => 18, 'user' => 5],
            ['item' => 7,  'qty' => 1, 'status' => 'In Progress', 'days' => 12, 'user' => 0],
            ['item' => 10, 'qty' => 2, 'status' => 'In Progress', 'days' => 7,  'user' => 1],
            ['item' => 13, 'qty' => 1, 'status' => 'In Progress', 'days' => 4,  'user' => 3],
            // Completed
            ['item' => 2,  'qty' => 3, 'status' => 'Completed',   'days' => 55, 'user' => 1],
            ['item' => 5,  'qty' => 2, 'status' => 'Completed',   'days' => 45, 'user' => 0],
            ['item' => 8,  'qty' => 2, 'status' => 'Completed',   'days' => 35, 'user' => 2],
            ['item' => 11, 'qty' => 1, 'status' => 'Completed',   'days' => 25, 'user' => 5],
            ['item' => 14, 'qty' => 2, 'status' => 'Completed',   'days' => 14, 'user' => 0],
        ];

        foreach ($maintenanceData as $data) {
            $item   = $items[$data['item']];
            $status = $data['status'];
            $qty    = $data['qty'];
            $m = Maintenance::create([
                'item_id'     => $item->id,
                'quantity'    => $qty,
                'date'        => $now->copy()->subDays($data['days'])->toDateString(),
                'status'      => $status,
                'description' => "Maintenance check for {$item->name}.",
                'user_id'     => $users[$data['user']]->id,
            ]);
            // Mirror controller logic: deduct stock for active maintenance
            if (in_array($status, ['Pending', 'In Progress'])) {
                Item::where('id', $item->id)->decrement('stock', $qty);
            }
            $ts = $now->copy()->subDays($data['days'])->toDateTimeString();
            DB::table('maintenances')->where('id', $m->id)->update(['created_at' => $ts, 'updated_at' => $ts]);
        }
    }
}
