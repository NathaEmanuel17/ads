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

    public function isPublished()
    {
        return $this->attributes['is_published'] ? '<span class="status-btn active-btn">'. lang('Adverts.text_is_published') .'</span>' : 
                                                   '<span class="status-btn close-btn">'. lang('Adverts.text_under_analysis') .'</span>';
    }

    public function address()
    {

        $number = !empty($this->attributes['number']) ? $this->attributes['number'] : 'N/A';

        return "{$this->attributes['street']} - {$number} - {$this->attributes['neighborhood']} - {$this->attributes['zipcode']} - {$this->attributes['city']} - {$this->attributes['state']}";
    }

    public function image(string $classImage = '', string $sizeImage = 'regular'): string
    {
        
        if(empty($this->attributes['images'])) {
            return $this->handleWithEmptyImage($classImage);
        }

        if(is_string($this->attributes['images'])) {

            return $this->handleWithSingleImage($classImage, $sizeImage);
        }

        if(url_is('api/adverts*')) {
            return $this->handleWithImagesForAPI();
        }

    }

    public function price()
    {
        return number_to_currency($this->attributes['price'], 'BRL', 'pt-BR', 2);
    }

    public function situation(): string
    {
        return $this->attributes['situation'] === 'new' ? '<span class="badge-success" >Novo</span>' : '<span class="badge-secondary" >Usado</span>';
    }

    public function displayPhone(): bool
    {
        return $this->attributes['display_phone'];
    }

    public function city()
    {
        return "{$this->attributes['city']} - {$this->attributes['state']}";
    }

    public function weMustNotifyThePublication(): bool
    {
        $originalIsPublished = (bool) $this->original['is_published'];

        return $originalIsPublished === false && $this->attributes['is_published'];
    }

    // Métodos privados

    private function handleWithEmptyImage(string $classImage): string
    {
        if(url_is('api/adverts*')) {

            return site_url('image/advert-no-image.png');
        }

        return img(
            [
                'src'   => route_to('web.image', 'advert-no-image.png', 'regular'),
                'alt'   => 'No image yet',
                'title' => 'No image yet',
                'class' => $classImage,
            ]
        );
    }

    private function handleWithSingleImage(string $classImage, string $sizeImage): string
    {
        if(url_is('api/adverts*')) {
            return $this->buildRouteForImageAPI($this->attributes['images']);
        }

        return img(
            [
                'src'   => route_to('web.image', $this->attributes['images'], $sizeImage),
                'alt'   => $this->attributes['title'],
                'title' => $this->attributes['title'],
                'class' => $classImage,
            ]
        );
    }

    private function handleWithImagesForAPI(): array
    {
        $images = [];

        foreach( $this->attributes['title'] as $image) {
           
            $images[] = $this->buildRouteForImageAPI($image->image);
        }
        return $images;
    }
   
    private function buildRouteForImageAPI(string $image): string
    {
        return site_url("image/{$image}");
    }
}
