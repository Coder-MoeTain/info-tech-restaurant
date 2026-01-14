<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Floor;
use App\Models\Table;
use App\Models\Category;
use App\Models\MenuItem;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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

        $floor = Floor::firstOrCreate(['name' => 'Main Floor']);
        Table::firstOrCreate(['floor_id' => $floor->id, 'name' => 'T1'], ['capacity' => 4]);
        Table::firstOrCreate(['floor_id' => $floor->id, 'name' => 'T2'], ['capacity' => 4]);

        $cat = Category::firstOrCreate(['name' => 'Starters']);
        MenuItem::firstOrCreate(['name' => 'Margherita Pizza'], [
            'category_id' => $cat->id,
            'price' => 8.50,
            'cost' => 4.00,
        ]);
        MenuItem::firstOrCreate(['name' => 'Caesar Salad'], [
            'category_id' => $cat->id,
            'price' => 6.00,
            'cost' => 2.50,
        ]);
    }
}
