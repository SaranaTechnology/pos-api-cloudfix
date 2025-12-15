<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            // Core permissions
            ['app' => 'core', 'function' => 'add-user'],
            ['app' => 'core', 'function' => 'edit-user'],
            ['app' => 'core', 'function' => 'view-user'],
            ['app' => 'core', 'function' => 'delete-user'],
            ['app' => 'core', 'function' => 'add-role'],
            ['app' => 'core', 'function' => 'edit-role'],
            ['app' => 'core', 'function' => 'change-role-permission'],
            ['app' => 'core', 'function' => 'delete-role'],
            ['app' => 'core', 'function' => 'set-user-role'],
            ['app' => 'core', 'function' => 'view-apps'],
            ['app' => 'core', 'function' => 'payment'],
            ['app' => 'core', 'function' => 'view-role'],
            ['app' => 'core', 'function' => 'view-permission'],

            // POS - Menu
            ['app' => 'pos', 'function' => 'view-menu'],
            ['app' => 'pos', 'function' => 'create-menu'],
            ['app' => 'pos', 'function' => 'update-menu'],
            ['app' => 'pos', 'function' => 'delete-menu'],

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

            // POS - Sales
            ['app' => 'pos', 'function' => 'view-sales'],
            ['app' => 'pos', 'function' => 'create-sales'],
            ['app' => 'pos', 'function' => 'cancel-sales'],
            ['app' => 'pos', 'function' => 'refund-sales'],
            ['app' => 'pos', 'function' => 'post-cogs'],

            // POS - Reports
            ['app' => 'pos', 'function' => 'view-sales-report'],
            ['app' => 'pos', 'function' => 'export-sales-report'],
        ];

        $now = now();
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['app' => $permission['app'], 'function' => $permission['function']],
                array_merge($permission, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('app', ['pos'])
            ->delete();
    }
};
