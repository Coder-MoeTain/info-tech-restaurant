<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Floor;
use App\Models\Modifier;
use App\Models\MenuItem;
use App\Models\RTable;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Printer;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $permissions = [
            'pos', 'checkout', 'refund', 'void', 'reports',
            'manage_menu', 'manage_tables', 'manage_users', 'kitchen_view', 'reprint'
        ];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $roles = [
            'admin' => ['*'],
            'cashier' => ['pos', 'checkout', 'reprint'],
            'waiter' => ['pos', 'kitchen_view'],
            'kitchen' => ['kitchen_view'],
            'manager' => ['reports', 'manage_menu', 'manage_tables', 'manage_users'],
        ];

        foreach ($roles as $role => $perms) {
            $r = Role::firstOrCreate(['name' => $role]);
            if ($perms === ['*']) {
                $r->givePermissionTo(Permission::all());
            } else {
                $r->syncPermissions($perms);
            }
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole('admin');

        // Additional users
        foreach (range(1, 5) as $i) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                ['name' => "User {$i}", 'password' => bcrypt('password')]
            );
            $role = $i % 2 === 0 ? 'cashier' : 'waiter';
            $user->assignRole($role);
        }

        // Printers
        $kitchenPrinter = Printer::firstOrCreate(
            ['name' => 'Kitchen Printer'],
            ['connection' => 'lan', 'ip' => '192.168.1.50', 'port' => 9100, 'is_kitchen' => true]
        );
        $cashierPrinter = Printer::firstOrCreate(
            ['name' => 'Cashier Printer'],
            ['connection' => 'lan', 'ip' => '192.168.1.60', 'port' => 9100, 'is_cashier' => true]
        );

        // Floors and tables (30 tables total)
        $floors = [];
        foreach (range(1, 3) as $f) {
            $floors[] = Floor::firstOrCreate(['name' => "Floor {$f}"]);
        }
        $tableCount = 0;
        foreach ($floors as $floor) {
            foreach (range(1, 10) as $t) {
                $tableCount++;
                RTable::firstOrCreate(
                    ['floor_id' => $floor->id, 'name' => "T{$tableCount}"],
                    ['capacity' => $faker->numberBetween(2, 6), 'status' => 'available']
                );
            }
        }

        // Categories and menu items (30 each)
        $categories = [];
        foreach (range(1, 30) as $i) {
            $categories[] = Category::firstOrCreate([
                'name' => "Category {$i}",
                'station' => $faker->randomElement(['hot', 'cold', 'bar']),
                'printer_id' => $i % 2 === 0 ? $kitchenPrinter->id : $cashierPrinter->id,
            ]);
        }

        $menuItems = [];
        foreach (range(1, 30) as $i) {
            $cat = $categories[array_rand($categories)];
            $menuItems[] = MenuItem::firstOrCreate([
                'name' => "Item {$i}",
            ], [
                'category_id' => $cat->id,
                'price' => $faker->randomFloat(2, 3, 20),
                'cost' => $faker->randomFloat(2, 1, 10),
                'stock' => $faker->numberBetween(20, 80),
                'low_stock_threshold' => $faker->numberBetween(5, 15),
            ]);
        }

        // Modifiers (30)
        foreach (range(1, 30) as $i) {
            Modifier::firstOrCreate(['name' => "Modifier {$i}"], ['price' => $faker->randomFloat(2, 0, 3)]);
        }

        // Seed sample orders (10) with items and payments
        foreach (range(1, 10) as $i) {
            $table = RTable::inRandomOrder()->first();
            $waiter = User::role('waiter')->inRandomOrder()->first();
            $order = Order::create([
                'order_number' => sprintf('SEED-%04d', $i),
                'table_id' => $table?->id,
                'order_type' => 'dine_in',
                'waiter_id' => $waiter?->id,
                'status' => 'paid',
                'subtotal' => 0,
                'grand_total' => 0,
                'paid_at' => now(),
            ]);
            $subtotal = 0;
            foreach (range(1, 2) as $j) {
                $item = $menuItems[array_rand($menuItems)];
                $qty = $faker->numberBetween(1, 3);
                $line = $qty * $item->price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'unit_price' => $item->price,
                    'line_total' => $line,
                    'note' => $faker->boolean ? 'No chili' : null,
                    'sent_to_kitchen_qty' => $qty,
                ]);
                $subtotal += $line;
            }
            $order->update([
                'subtotal' => $subtotal,
                'grand_total' => $subtotal,
            ]);
            Payment::create([
                'order_id' => $order->id,
                'method' => 'cash',
                'amount' => $subtotal,
            ]);
        }
    }
}
