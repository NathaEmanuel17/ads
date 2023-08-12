<?php

use App\Services\AdvertService;
use App\Services\CategoryService;
use CodeIgniter\Config\Factories;

if (!function_exists('categories_adverts')) {

    function categories_adverts(int $limit = 5)
    {

        return Factories::class(CategoryService::class)->getCategoriesFromPublishedAdverts($limit);
    }
}

if (!function_exists('cities_adverts')) {

    function cities_adverts(int $limit = 5, string $categorySlug = null)
    {

        return Factories::class(AdvertService::class)->getCitiesFromPublishedAdevrts($limit, $categorySlug);
    }
}
