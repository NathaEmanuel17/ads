<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Category extends Entity
{
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];

    public function recover()
    {
        $this->attributes['deleted_at'] = null;
    }
}
