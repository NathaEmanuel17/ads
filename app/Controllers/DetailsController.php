<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\AdvertService;
use App\Services\ImageService;
use CodeIgniter\Config\Factories;

class DetailsController extends BaseController
{

    private $advertService;

    public function __construct()
    {
        $this->advertService = Factories::class(AdvertService::class);
    }

    public function details(string $code = null)
    {
        $advert = $this->advertService->getAdvertByCode($code);

        $criteria = [
            'categories.slug' => $advert->category_slug,
            'adverts.code !=' => $advert->code,
        ];

        $advertsFromSameCategory = (object) $this->advertService->getAllAdvertsPaginated(perPage: 10, criteria: $criteria);

         $data = [
            'title'       => "Detalhes do anúncio {$advert->title}",
            'advert'      => $advert,
            'moreAdverts' => $advertsFromSameCategory->adverts,
            'pager'       => $advertsFromSameCategory->pager,
         ];

         return view('Web/Details/index', $data);
    }

    public function image(string $image = null, string $sizeImage = 'regular')
    {
        ImageService::showImage('adverts', $image, $sizeImage);
        
    }


}
