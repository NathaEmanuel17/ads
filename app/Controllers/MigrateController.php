<?php

namespace App\Controllers;

use App\Database\Seeds\GeneralSeeder;
use CodeIgniter\Controller;
use Throwable;

class MigrateController extends Controller
{
    public function index()
    {

        $migrate = \Config\Services::migrations();

        try {
            $migrate->latest();
        } catch (Throwable $e) {
            die($e->getMessage());
        }
    }

    public function seed()
    {

        $seeder = \Config\Database::seeder();
        
        try {
            $seeder->call(GeneralSeeder::class);
        } catch (Throwable $e) {
            die($e->getMessage());
        }
    }
}