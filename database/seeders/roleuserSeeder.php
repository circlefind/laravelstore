<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class roleuserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roleusers')->insert([
            [
                'id' => '0',
                'role' => 'ADMINISTRATOR',
            ],
            [
                'id' => '99',
                'role' => 'USER',
            ]
        ]);
    }
}
