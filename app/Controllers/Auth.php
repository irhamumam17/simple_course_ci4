<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class Auth extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        //
    }

    public function login()
    {
        helper(['form']);
        $input = $this->validate([
            'email' => 'required|valid_email',
            'password' => 'required'
        ]);

        if (!$input) {
            return $this->respond(['errors' => Services::validation()->getErrors()], 400);
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $userModel = new User();
        $user = $userModel->where('email', $email)->first();
        if ($user) {
            if (password_verify($password, $user['password'])) {
                return $this->respond($user, 200, 'Login Success');
            }
            return $this->respond(status: 401, message: 'Password is wrong');
        }
        return $this->respond(status: 401, message: 'Email is wrong');
    }

    public function register()
    {
        helper(['form']);
        $input = $this->validate([
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]'
        ]);

        if(!$input)
        {
            return $this->respond(['errors' => Services::validation()->getErrors()], 400);
        }

        $name = $this->request->getVar('name');
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $password_confirmation = $this->request->getVar('password_confirmation');

        $userModel = new User();
        $user = $userModel->where('email', $email)->first();
        if ($user) {
            return $this->respond(status: 401, message: 'Email is already used');
        }

        try {
            $data = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password_confirmation
            ];
            if ($userModel->insert($data)) {
                return $this->respond(data: [], status: 200, message: "User created successfully.");
            };
            return $this->respond($userModel->errors(), 400, 'User failed to create');
        } catch (\Throwable $th) {
            return $this->respond(status: 400, message: 'Failed to create user');
        }
    }
}
