<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    $this->call([
        regionSeeder::class,
        configSeeder::class,
        roleuserSeeder::class,
        UserSeeder::class,
    ]);
    }
}
