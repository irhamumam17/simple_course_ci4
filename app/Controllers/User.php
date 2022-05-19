<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\User as UserModel;

class User extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        try {
            $users = new UserModel();
            return $this->respond($users->orderBy('id', 'DESC')->findAll(), 200, 'User');
        } catch (\Exception $e) {
            return $this->respond(status: 500, message: $e->getMessage());
        }
    }

    public function create()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]'
        ]);

        $name = $this->request->getVar('name');
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        if ($user) {
            return $this->respond(status: 401, message: 'Email is already used');
        }

        try {
            $userModel->insert([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]);
            return $this->respond(status: 200, message: 'User created');
        } catch (\Exception $e) {
            return $this->respond(status: 500, message: $e->getMessage());
        }
    }

    
    public function show($id = null)
    {
        try {
            $users = new UserModel();
            $user = $users->find($id);
            if ($user) {
                return $this->respond($user, 200, 'User');
            }
            return $this->respond(status: 404, message: 'User not found');
        } catch (\Exception $e) {
            return $this->respond(status: 500, message: $e->getMessage());
        }
    }

    public function update($id = null)
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]'
        ]);

        $name = $this->request->getVar('name');
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user) {
            $user->name = $name;
            $user->email = $email;
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->save();
            return $this->respond($user, 200, 'User updated');
        }
        return $this->respond(status: 404, message: 'User not found');
    }

    public function delete($id = null)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user) {
            $user->delete();
            return $this->respond(status: 200, message: 'User deleted');
        }
        return $this->respond(status: 404, message: 'User not found');
    }
}
