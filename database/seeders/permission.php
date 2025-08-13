<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infra\Roles\Models\Permissions\Permissions;

class permission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permissions::query()->delete();
        Permissions::create([
            'function' => 'add-user',
            'app' => 'core',
        ]);
        Permissions::create([
            'function' => 'edit-user',
            'app' => 'core',
        ]);

        Permissions::create([
            'function' => 'view-user',
            'app' => 'core',
        ]);

        Permissions::create([
            'function' => 'delete-user',
            'app' => 'core',
        ]);

        Permissions::create([
            'function' => 'add-role',
            'app' => 'core',
        ]);

        Permissions::create([
            'function' => 'edit-role',
            'app' => 'core',
        ]);

        Permissions::create([
            'function' => 'change-role-permission',
            'app' => 'core',
        ]);

        Permissions::create([
            'function' => 'delete-role',
            'app' => 'core',
        ]);

        Permissions::create([
            'function' => 'set-user-role',
            'app' => 'core',
        ]);
        Permissions::create([
            'function' => 'view-apps',
            'app' => 'core',
        ]);
        Permissions::create([
            'function' => 'payment',
            'app' => 'core',
        ]);
        Permissions::create([
            'function' => 'view-role',
            'app' => 'core',
        ]);
        Permissions::create([
            'function' => 'view-permission',
            'app' => 'core',
        ]);
        
    }
}
