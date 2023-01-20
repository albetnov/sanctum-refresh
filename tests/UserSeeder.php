<?php

namespace Albet\SanctumRefresh\Tests;

use Albet\SanctumRefresh\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Sang Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('admin12345'),
        ]);
    }
}
