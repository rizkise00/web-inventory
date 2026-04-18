<?php

namespace Database\Seeders;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_approved' => true,
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_approved' => true,
        ]);

        // Create pending user
        User::create([
            'name' => 'Pending User',
            'email' => 'pending@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_approved' => false,
        ]);

        // Create sample stock in records
        StockIn::create([
            'item_name' => 'Laptop Dell XPS 13',
            'quantity' => 10,
            'supplier' => 'PT. Elektronik Jaya',
            'date' => now()->subDays(5),
            'notes' => 'New batch arrival',
            'user_id' => $admin->id,
        ]);

        StockIn::create([
            'item_name' => 'Mouse Logitech MX Master',
            'quantity' => 25,
            'supplier' => 'PT. Computer Supply',
            'date' => now()->subDays(3),
            'notes' => 'Restocking',
            'user_id' => $admin->id,
        ]);

        StockIn::create([
            'item_name' => 'Keyboard Mechanical Keychron',
            'quantity' => 15,
            'supplier' => 'PT. Elektronik Jaya',
            'date' => now()->subDays(1),
            'notes' => null,
            'user_id' => $user->id,
        ]);

        // Create sample stock out records
        StockOut::create([
            'item_name' => 'Laptop Dell XPS 13',
            'quantity' => 2,
            'recipient' => 'John Doe - IT Department',
            'date' => now()->subDays(2),
            'notes' => 'For new employees',
            'user_id' => $admin->id,
        ]);

        StockOut::create([
            'item_name' => 'Mouse Logitech MX Master',
            'quantity' => 5,
            'recipient' => 'Jane Smith - HR Department',
            'date' => now()->subDays(1),
            'notes' => null,
            'user_id' => $user->id,
        ]);

        StockOut::create([
            'item_name' => 'Keyboard Mechanical Keychron',
            'quantity' => 3,
            'recipient' => 'Bob Wilson - Finance',
            'date' => now(),
            'notes' => 'Replacement for old keyboards',
            'user_id' => $admin->id,
        ]);
    }
}
