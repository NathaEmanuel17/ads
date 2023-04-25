<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Advert extends Entity
{
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'user_since'];
    protected $casts   = [
        'is_published'      => 'boolean',
        'adverts'           => '?integer', // pode ou não ser null
        'display_phone'     => 'boolean',
    ];

    public function setPrice(string $price)
    {
        $this->attributes['price'] = str_replace(',','', $price);
    }

    // Esse método será utilizado pelo manager para publicar ou não um anúncio
    public function setIsPublished(string $isPublished)
    {
        $this->attributes['is_published'] = $isPublished ? true : false;
    }

    public function recover()
    {
        $this->attributes['deleted_at'] = null;
    }

    public function unsetAuxiliaryAttributes()
    {
        //unset($this->attributes['address']);
        unset($this->attributes['images']);
    }

    public function image()
    {
        return 'imagem';
    }

    public function isPublished()
    {
        return 'Publicado ou não';
    }

    public function address()
    {
        return 'Endereço';
    }
}
