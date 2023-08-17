<?php

namespace App\Controllers\API\V1;

use App\Controllers\BaseController;
use App\Entities\Advert;
use App\Requests\AdvertRequest;
use App\Services\AdvertService;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Config\Factories;

class AdvertsUserController extends BaseController
{

    use ResponseTrait;

    private $advertService;

    public function __construct()
    {
        $this->advertService = Factories::class(AdvertService::class);
    }

    public function index()
    {
        $perPage = $this->request->getGet('perPage');
        $page    = $this->request->getGet('page');

        $adverts = (object) $this->advertService->getAllAdvertsForUserAPI(perPage: $perPage, page: $page);

        return $this->respond(
            [
                'code'    => 200,
                'adverts' => $adverts->adverts,
                'pager'   => $adverts->pager
            ]
        );
    }

    public function getUserAdvert(int $advertID = null)
    {

        return $this->respond(
            [
                'code'      => 200,
                'advert'    => $this->advertService->getAdvertByID($advertID)
            ]
        );
    }

    public function deleteUserAdvert(int $advertID = null)
    {
        $advert = $this->advertService->getAdvertByID($advertID);

        $this->advertService->tryDeleteAdvert($advert->id, wantValidadeAdvert: false);

        return $this->respondDeleted(
            [
                'code'      => 200,
                'message'   => lang('App.success_deleted')
            ]
        );
    }

    public function updateUserAdvert(int $advertID = null)
    {
        $advert = $this->advertService->getAdvertByID($advertID);

        $request = (array) $this->request->getJSON();

        $advert->fill($request);

        $this->advertService->trySaveAdvert($advert);

        return $this->respondUpdated(
            [
                'code'      => 200,
                'message'   => lang('App.success_saved')
            ]
        );
    }

    public function createUserAdvert()
    {
        Factories::class(AdvertRequest::class)->validateBeforeSave('advert');

        $advert = new Advert($this->request->getVar());

        $this->advertService->trySaveAdvert($advert);

        return $this->respondCreated(
            [
                'code'      => 201,
                'message'   => lang('App.success_saved')
            ]
        );
    }
}
