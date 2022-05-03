<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory([
            'name' => 'Radu Aprofiri',
            'email' => 'raduaprofiri@gmail.com',
            'password' => Hash::make('secret')
        ])->create();
    }
}
