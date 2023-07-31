<?php

namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Database\Seeder;
use CodeIgniter\Test\Fabricator;
use CodeIgniter\CLI\CLI;

class UserSeeder extends Seeder
{
    public function run()
    {
        try {
            $this->db->transStart();

            $createHowManyUsers = 500;

            $fabricator  = new Fabricator(UserModel::class);
            $users       = $fabricator->make($createHowManyUsers);

            $userModel   = Factories::models(UserModel::class);

            $totalSteps = count($users);
            $currStep   = 1;

            foreach($users as $user) {
                CLI::showProgress($currStep++, $totalSteps);
                $userModel->insert($user);
            }

            CLI::showProgress(false);

            $this->db->transComplete();

            echo 'Anunciantes criados com sucesso!';
            
        } catch (\Throwable $th) {
            print $th;
        }
    }
}
