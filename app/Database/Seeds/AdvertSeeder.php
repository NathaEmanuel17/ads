<?php

namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Database\Seeder;

class AdvertSeeder extends Seeder
{
    public function run()
    {
        try {

            $this->db->transStart();

            $categories    = $this->db->table('categories')->select('id')->get()->getResultArray();
            $categoriesIDS = array_column($categories, 'id');

            helper('superadmin');
            $userManager = get_superadmin();

            $anunciantes    = $this->db->table('users')->select('id')->where('id !=', $userManager->id)->orderBy('id', 'ASC')->get()->getResultArray();
            $anunciantesIDS = array_column($anunciantes, 'id');

            echo '<pre>';
            print_r($anunciantes);
            exit;
            $this->db->transComplete();

            echo 'Anuncios criadas com sucesso!';
        } catch (\Throwable $th) {
            print $th;
        }
    }
}
