<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Services\AdvertService;
use CodeIgniter\Config\Factories;

class AdvertsUserController extends BaseController
{
    private $advertService;

    public function __construct()
    {
        $this->advertService = Factories::class(AdvertService::class);
    }

    public function index()
    {
        return view('Dashboard/Adverts/index');
    }

    public function getUserAdverts()
    {
        if(!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $response = [
            'data' => $this->advertService->getAllAdverts(classBtnAction: 'btn btn-sm btn-outline-primary'),
        ];
    
        return $this->response->setJSON($response);
    }
}
