<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Matkul as MatkulModel;
use App\Models\User;
use Config\Services;

class Matkul extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        try {
            $matkul = new MatkulModel();
            return $this->respond($matkul->orderBy('id', 'DESC')->findAll(), 200, 'Matkul');
        } catch (\Exception $e) {
            return $this->respond(status: 400, message: $e->getMessage());
        }
    }

    public function create()
    {
        helper(['form']);
        $input = $this->validate([
            'name' => 'required',
            'grade' => 'required',
            'user_id' => 'required',
        ]);

        if(!$input)
        {
            return $this->respond(['errors' => Services::validation()->getErrors()], 400);
        }

        $name = $this->request->getVar('name');
        $grade = $this->request->getVar('grade');
        $user_id = $this->request->getVar('user_id');

        $matkul = new MatkulModel();
        $matkul = $matkul->where('name', $name)->first();
        if ($matkul) {
            return $this->respond(status: 401, message: 'Nama Matkul is already used');
        }

        try {
            $matkul->insert([
                'name' => $name,
                'grade' => $grade,
                'user_id' => $user_id
            ]);
            return $this->respond(status: 200, message: 'Matkul created');
        } catch (\Exception $e) {
            return $this->respond(status: 500, message: $e->getMessage());
        }
    }

    public function show($id = null)
    {
        try {
            $matkul = new MatkulModel();
            $matkul = $matkul->find($id);
            if ($matkul) {
                return $this->respond($matkul, 200, 'Matkul');
            } else {
                return $this->respond(status: 404, message: 'Matkul not found');
            }
        } catch (\Exception $e) {
            return $this->respond(status: 500, message: $e->getMessage());
        }
    }

    public function update($id = null)
    {
        helper(['form']);
        $input = $this->validate([
            'name' => 'required',
            'grade' => 'required',
            'user_id' => 'required',
        ]);

        if(!$input)
        {
            return $this->respond(['errors' => Services::validation()->getErrors()], 400);
        }

        $name = $this->request->getVar('name');
        $grade = $this->request->getVar('grade');
        $user_id = $this->request->getVar('user_id');

        $matkul = new MatkulModel();
        try {
            $matkul->update([
                'name' => $name,
                'grade' => $grade,
                'user_id' => $user_id
            ]);
            return $this->respond(status: 200, message: 'Matkul updated');
        } catch (\Exception $e) {
            return $this->respond(status: 500, message: $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        try {
            $matkul = new MatkulModel();
            $matkul = $matkul->find($id);
            if ($matkul) {
                $matkul->delete();
                return $this->respond(status: 200, message: 'Matkul deleted');
            } else {
                return $this->respond(status: 404, message: 'Matkul not found');
            }
        } catch (\Exception $e) {
            return $this->respond(status: 500, message: $e->getMessage());
        }
    }

    public function indexByUser($id = null)
    {
        try {
            $matkulModel = new MatkulModel();
            $matkuls = $matkulModel->where('user_id', $id)->findAll();
            return $this->respond($matkuls, 200, 'Mata Kuliah Retrieved Successfully');
        } catch (\Throwable $th) {
            return $this->respond(status: 400, message: $th->getMessage());
        }
    }
    
    public function createByUser($id = null)
    {
        helper(['form']);
        $input = $this->validate([
            'name' => 'required',
            'grade' => 'required',
        ]);

        if(!$input)
        {
            return $this->respond(['errors' => Services::validation()->getErrors()], 400);
        }

        if($id == null){
            return $this->respond(status:404, message: 'Id User cannot be null');
        }

        $userModel = new User();
        $user = $userModel->find($id);

        if(!$user){
            return $this->respond(status: 404, message: "User not found");
        }

        $name = $this->request->getVar('name');
        $grade = $this->request->getVar('grade');

        $matkulModel = new MatkulModel();
        $matkul = $matkulModel->where('name', $name)->first();
        if ($matkul) {
            return $this->respond(status: 401, message: 'Nama Matkul is already used');
        }

        try {
            $matkulModel->insert([
                'name' => $name,
                'grade' => $grade,
                'user_id' => $id
            ]);
            return $this->respond([],status: 200, message: 'Matkul created');
        } catch (\Exception $e) {
            return $this->respond(status: 500, message: $e->getMessage());
        }
    }

    public function updateByUser($id_user = null, $id_matkul = null)
    {
        helper(['form']);
        $input = $this->validate([
            'name' => 'required',
            'grade' => 'required',
        ]);

        if(!$input)
        {
            return $this->respond(['errors' => Services::validation()->getErrors()], 400);
        }

        if($id_user == null){
            return $this->respond(status:404, message: 'Id User cannot be null');
        }

        if($id_matkul == null){
            return $this->respond(status:404, message: 'Id Matkul cannot be null');
        }

        $userModel = new User();
        $user = $userModel->find($id_user);

        if(!$user){
            return $this->respond(status: 404, message: "User not found");
        }

        $matkulModel = new MatkulModel();
        $matkul = $matkulModel->find($id_matkul);

        if(!$matkul)
        {
            return $this->respond(status: 404, message: "Matkul not found");
        }

        if($user['id'] !== $matkul['user_id'])
        {
            return $this->respond(status: 401, message: "You are not authorized to update this matkul");
        }
        $name = $this->request->getVar('name');
        $grade = $this->request->getVar('grade');

        try {
            $matkulModel->update($id_matkul,[
                'name' => $name,
                'grade' => $grade,
            ]);
            return $this->respond(status: 200, message: 'Matkul updated');
        } catch (\Exception $e) {
            return $this->respond(status: 400, message: $e->getMessage());
        }
    }

    public function deleteByUser($id_user = null, $id_matkul = null)
    {
        if($id_user == null){
            return $this->respond(status:404, message: 'Id User cannot be null');
        }

        if($id_matkul == null){
            return $this->respond(status:404, message: 'Id Matkul cannot be null');
        }

        $userModel = new User();
        $user = $userModel->find($id_user);

        if(!$user){
            return $this->respond(status: 404, message: "User not found");
        }

        $matkulModel = new MatkulModel();
        $matkul = $matkulModel->find($id_matkul);

        if(!$matkul)
        {
            return $this->respond(status: 404, message: "Matkul not found");
        }

        try {
            $matkulModel->delete($id_matkul);
            return $this->respond(status: 200, message: 'Matkul deleted');
        } catch (\Exception $e) {
            return $this->respond(status: 400, message: $e->getMessage());
        }
    }
}
