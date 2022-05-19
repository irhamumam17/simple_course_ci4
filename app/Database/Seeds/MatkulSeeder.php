<?php

namespace App\Database\Seeds;

use App\Models\Matkul;
use App\Models\User;
use CodeIgniter\Database\Seeder;

class MatkulSeeder extends Seeder
{
    public function run()
    {
        $user_model = new User();
        $matkul_model = new Matkul();
        $data = [
            'name' => 'Ari',
            'grade' => 78,
            'user_id' => $user_model->first()['id']
        ];
        $matkul_model->insert($data);
    }
}
