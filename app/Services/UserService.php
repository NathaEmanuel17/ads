<?php 

namespace App\Services;

use App\Models\UserModel;
use CodeIgniter\Config\Factories;
use Fluent\Auth\Facades\Hash;

class UserService
{
    private $userModel;
    private $user;

    public function __construct()
    {
        $this->userModel = Factories::models(UserModel::class);
        $this->user      = service('auth')->user();
    }

    public function userDataIsComplete(): bool
    {
        if(
            is_null($this->user->name) || 
            is_null($this->user->last_name) || 
            is_null($this->user->cpf) ||
            is_null($this->user->birth) ||
            is_null($this->user->phone)
        ){
            return false;
        }

        return true;
    }

    public function tryUpdateProfile(array $request)
    {

        try {
            
            $request = (object)$request;

            $this->user->name          = $request->name;
            $this->user->last_name     = $request->last_name;
            $this->user->cpf           = $request->cpf;
            $this->user->email         = $request->email;
            $this->user->phone         = $request->phone;
            $this->user->birth         = $request->birth;
            $this->user->display_phone = $request->display_phone;
     
            if($this->user->hasChanged()) {

                $this->userModel->save($this->user);
                
            }
        } catch (\Exception $e) {
            die('Não foi possivel atualizar o perfil');
        }
    }

    public function currentPasswordIsValid(string $currentPassword): bool
    {
        return Hash::check($currentPassword, $this->user->password);
    }

    public function tryUpdateAccess(string $newPassword)
    {

        try {
            
            $this->user->password  = $newPassword;
     
            if($this->user->hasChanged()) {

                $this->userModel->save($this->user);
                
            }
        } catch (\Exception $e) {
            die('Não foi possivel atualizar o seu acesso');
        }
    }
}