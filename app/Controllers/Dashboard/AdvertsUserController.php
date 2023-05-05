<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Entities\Advert;
use App\Requests\AdvertRequest;
use App\Services\AdvertService;
use App\Services\CategoryService;
use App\Services\ImageService;
use CodeIgniter\Config\Factories;

class AdvertsUserController extends BaseController
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
        return view('Dashboard/Adverts/index');
    }

    public function getUserAdverts()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $response = [
            'data' => $this->advertService->getAllAdverts(classBtnAction: 'btn btn-sm btn-outline-primary'),
        ];

        return $this->response->setJSON($response);
    }

    public function getUserAdvert()
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

    public function createUserAdvert()
    {
        $this->advertRequest->validateBeforeSave('advert');

        $this->advertService->trySaveAdvert(new Advert($this->removeSpoofingFromRequest()));

        return $this->response->setJSON($this->advertRequest->respondWithMessage(message: lang('App.success_saved')));
    }

    public function updateUserAdvert()
    {
        $this->advertRequest->validateBeforeSave('advert');

        $advert = $this->advertService->getAdvertByID($this->request->getGetPost('id'));

        $advert->fill($this->removeSpoofingFromRequest());

        $this->advertService->trySaveAdvert($advert);

        return $this->response->setJSON($this->advertRequest->respondWithMessage(message: lang('App.success_saved')));

    }

    public function getCategoriesEndSituations()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        
        $options = [
            'class'       => 'form-control',
            'placeholder' => lang('Categories.label_choose_category'),
            'selected'    => ''
        ];



        $response = [
            'situations'  => $this->advertService->getDropdownSituations(),
            'categories'  => $this->categoryService->getMultinivel('category_id', $options)
        ];

        return $this->response->setJSON($response);
    }

    public function editUserAdvertImages(int $id = null)
    {
        $data = [
            'advert'        => $advert = $this->advertService->getAdvertByID($id),
            'hiddens'       => ['_method' => 'PUT'],    // Para o upload de imagens (editando um anúncio)
            'hiddensDelete' => ['id' => $advert->id,'_method' => 'DELETE'], // Para remover as imagens do anuncio
        ];

        return view('Dashboard/Adverts/edit_images', $data);
    }

    public function uploadAdvertImages(int $id = null)
    {
        $this->advertRequest->validateBeforeSave('advert_images', respondWithRedirect: true);
        
        $this->advertService->tryStoreAdvertImages($this->request->getFiles('images'), $id);
        
        return redirect()->back()->with('success', lang('App.success_saved'));
    }

    public function deleteUserAdvertImage(string $image = null)
    {
        $this->advertService->tryDeleteAdvertImage($this->request->getGetPost('id'), $image);
        
        return redirect()->back()->with('success', lang('App.success_deleted'));
    }
    
}
