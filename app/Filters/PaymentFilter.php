<?php

namespace App\Filters;

use App\Services\GerencianetService;
use CodeIgniter\Config\Factories;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

class PaymentFilter implements FilterInterface
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

            $userSubscription = $this->gerencianetService->getUserSubscription();

            if(!$userSubscription->is_paid) {
                
                if(url_is('api*')) {
                  
                    return $this->fail('Humm... ainda nãio identificamos o pagamento do seu Plano!', ResponseInterface::HTTP_UNAUTHORIZED);  
                }

                return redirect()->back()->with('danger', 'Humm... ainda nãio identificamos o pagamento do seu Plano!');
            }
        }
    }

 
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
