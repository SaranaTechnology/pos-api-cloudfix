<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infra\DataSso\Models\DataSso as ModelsDataSso;

class DataSso extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ModelsDataSso::create([
            'name' => 'core',
            'url' => 'https://core.pendidikancerdas.org/core/dashboard',
        ]);
    }
}
