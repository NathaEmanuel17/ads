<?php

use CodeIgniter\Config\Factories;
use Fluent\Auth\Models\UserModel;

if(!function_exists('get_superadmin')) {
    function get_superadmin()
    {
        return Factories::models(UserModel::class)->getSuperadmin();
    }
}

?>