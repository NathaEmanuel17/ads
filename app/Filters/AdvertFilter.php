<?php

namespace App\Filters;

use App\Services\GerencianetService;
use CodeIgniter\Config\Factories;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

class AdvertFilter implements FilterInterface
{

    use ResponseTrait;

    protected $response;
    protected $gerencianetService;

    public function __construct()
    {
        $this->response           = service('response');
        $this->gerencianetService = Factories::class(GerencianetService::class);
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        if ($this->gerencianetService->userReachedAdvertsLimit()) {
            // contamos quantos anúncios o user logado possui
            $countUserAdverts    = $this->gerencianetService->countAllUserAdverts();
            $countFeaturesAdvers = $this->gerencianetService->getUserSubscription()->features->adverts;

            if (url_is('api/adverts/create*' || $request->isAjax())) {

                return $this->fail("Você já cadastriu {$countUserAdverts} anúncios. Seu plano contempla o cadastro de {$countFeaturesAdvers} anúncios. Para continuar, você precisara migrar de Plano", ResponseInterface::HTTP_UNAUTHORIZED);
            }

            return redirect()->back()->with('danger', "Você já cadastriu {$countUserAdverts} anúncios. Seu plano contempla o cadastro de {$countFeaturesAdvers} anúncios. Para continuar, você precisara migrar de Plano");
        }
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
