<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class permission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // POS - Menu
            ['app' => 'pos', 'function' => 'view-menu'],
            ['app' => 'pos', 'function' => 'create-menu'],
            ['app' => 'pos', 'function' => 'update-menu'],
            ['app' => 'pos', 'function' => 'delete-menu'],

            // POS - Category Menu
            ['app' => 'pos', 'function' => 'view-category-menu'],
            ['app' => 'pos', 'function' => 'create-category-menu'],
            ['app' => 'pos', 'function' => 'update-category-menu'],
            ['app' => 'pos', 'function' => 'delete-category-menu'],

            // POS - Combo
            ['app' => 'pos', 'function' => 'view-combo'],
            ['app' => 'pos', 'function' => 'create-combo'],
            ['app' => 'pos', 'function' => 'update-combo'],
            ['app' => 'pos', 'function' => 'delete-combo'],

            // POS - Customer
            ['app' => 'pos', 'function' => 'view-customer'],
            ['app' => 'pos', 'function' => 'create-customer'],
            ['app' => 'pos', 'function' => 'update-customer'],
            ['app' => 'pos', 'function' => 'delete-customer'],
            ['app' => 'pos', 'function' => 'manage-loyalty'],

            // POS - Table
            ['app' => 'pos', 'function' => 'view-table'],
            ['app' => 'pos', 'function' => 'create-table'],
            ['app' => 'pos', 'function' => 'update-table'],
            ['app' => 'pos', 'function' => 'delete-table'],

            // POS - Sales/Transaction
            ['app' => 'pos', 'function' => 'view-sales'],
            ['app' => 'pos', 'function' => 'create-sales'],
            ['app' => 'pos', 'function' => 'update-sales'],
            ['app' => 'pos', 'function' => 'cancel-sales'],
            ['app' => 'pos', 'function' => 'refund-sales'],
            ['app' => 'pos', 'function' => 'post-cogs'],
            ['app' => 'pos', 'function' => 'apply-discount'],

            // POS - Payment
            ['app' => 'pos', 'function' => 'view-payment-method'],
            ['app' => 'pos', 'function' => 'create-payment-method'],
            ['app' => 'pos', 'function' => 'update-payment-method'],
            ['app' => 'pos', 'function' => 'delete-payment-method'],

            // POS - Shift/Cashier
            ['app' => 'pos', 'function' => 'view-shift'],
            ['app' => 'pos', 'function' => 'open-shift'],
            ['app' => 'pos', 'function' => 'close-shift'],

            // POS - Printer
            ['app' => 'pos', 'function' => 'view-printer'],
            ['app' => 'pos', 'function' => 'create-printer'],
            ['app' => 'pos', 'function' => 'update-printer'],
            ['app' => 'pos', 'function' => 'delete-printer'],

            // POS - Reports
            ['app' => 'pos', 'function' => 'view-sales-report'],
            ['app' => 'pos', 'function' => 'export-sales-report'],
            ['app' => 'pos', 'function' => 'view-dashboard'],
        ];

        $now = now();
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['app' => $permission['app'], 'function' => $permission['function']],
                array_merge($permission, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }
}
