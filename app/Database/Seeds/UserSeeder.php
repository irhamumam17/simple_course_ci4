<?php

namespace App\Database\Seeds;

use App\Models\User;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name' => 'Ari',
            'email' => 'ari@gmail.com',
            'password' => 'rahasia123'
        ];
        $user_model = new User();
        $user_model->insert($data);
    }
}
