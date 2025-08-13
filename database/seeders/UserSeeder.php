<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infra\Roles\Models\Roles;
use Infra\User\Models\User;
use Infra\User\Models\UserRoles\UserRoles;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'password' => bcrypt('sekolahcerdas'),
            'email' => 'admin@admin.com',
        ]);
        Roles::create([
            'nama' => 'Super Admin',
        ]);
        UserRoles::create([
            'user_id' => 1,
            'roles_id' => 1,
        ]);
    }
}
