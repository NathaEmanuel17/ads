<?php

namespace App\Filters;

use App\Services\GerencianetService;
use CodeIgniter\Config\Factories;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

class HasSubscriptionFilter implements FilterInterface
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
        if(!$request->isAJAX()) {

            if(!$this->gerencianetService->userHasSubscription()) {
                
                if(url_is('api*')) {
                  
                    return $this->fail('Humm... você ainda não possui um plano bem lindão', ResponseInterface::HTTP_UNAUTHORIZED);  
                }

                return redirect()->back()->with('danger', 'Humm... você ainda não possui um plano bem lindão');
            }
        }
    }

 
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
