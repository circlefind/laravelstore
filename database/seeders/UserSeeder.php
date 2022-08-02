<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
        [
            'id' => '00000',
            'name' => 'Administrator',
            'email' => 'admin@elimangga.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'idroleuser' => '0',
            'remember_token' => Str::random(10)
        ]
    );
    }
}
