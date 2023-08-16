<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Requests\AdvertRequest;
use App\Services\AdvertService;
use App\Services\CategoryService;
use CodeIgniter\Config\Factories;

class AdvertsManagerController extends BaseController
{
    private $advertService;
    private $categoryService;
    private $advertRequest;

    public function __construct()
    {
        $this->advertService   = Factories::class(AdvertService::class);
        $this->advertRequest   = Factories::class(AdvertRequest::class);
        $this->categoryService = Factories::class(CategoryService::class);
    }

    public function index()
    {
        return view('Manager/adverts/index');
    }

    public function archived()
    {
        return view('Manager/adverts/archived');
    }

    public function getManagerArchivedAdverts()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $response = [
            'data' => $this->advertService->getArchivedAdverts(showBtnRecover: false, classBtnAction: 'btn btn-sm btn-outline-info'),
        ];

        return $this->response->setJSON($response);
    }


    public function getAllAdverts()
    {
        if (!$this->request->isAJAX()) {

            return redirect()->back();
        }

        return $this->response->setJSON(['data' => $this->advertService->getAllAdverts(showBtnArchive: true, showBtnQuestion: false, showBtnViewAdvert: false)]);
    }

    public function getManagerAdvert()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $advert = $this->advertService->getAdvertByID($this->request->getGetPost('id'));


        $options = [
            'class'       => 'form-control',
            'placeholder' => lang('Categories.label_choose_category'),
            'selected'    => !(empty($advert->category_id)) ? $advert->category_id : ''
        ];



        $response = [
            'advert'      => $advert,
            'situations'  => $this->advertService->getDropdownSituations($advert->situation),
            'categories'  => $this->categoryService->getMultinivel('category_id', $options)
        ];

        return $this->response->setJSON($response);
    }

    public function updateManagerAdvert()
    {
        $this->advertRequest->validateBeforeSave('advert');

        $advert = $this->advertService->getAdvertByID($this->request->getGetPost('id'));

        $advert->fill($this->removeSpoofingFromRequest());

        $this->advertService->trySaveAdvert($advert, protect: false, notifyUserIfPublished: true);

        return $this->response->setJSON($this->advertRequest->respondWithMessage(message: lang('App.success_saved')));

    }
    
    public function archiveManagerAdvert()
    {
        $this->advertService->tryArchiveAdvert($this->request->getGetPost('id'));

        return $this->response->setJSON($this->advertRequest->respondWithMessage(message: lang('App.success_archived')));
    }

    public function deleteManagerAdvert()
    {
        $this->advertService->tryDeleteAdvert($this->request->getGetPost('id'));

        return $this->response->setJSON($this->advertRequest->respondWithMessage(message: lang('App.success_deleted')));
    }

    public function showManagerdvertImages(int $id = null)
    {
        $data = [
            'advert' => $advert = $this->advertService->getAdvertByID($id),
        ];

        return view('Manager/Adverts/show_images', $data);
    }
}
