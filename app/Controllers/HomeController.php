<?php

namespace App\Controllers;

use App\Requests\GerencianetRequest;
use App\Services\AdvertService;
use App\Services\GerencianetService;
use App\Services\PlanService;
use App\Services\UserService;
use CodeIgniter\Config\Factories;

class HomeController extends BaseController
{

    private $planService;
    private $userService;
    private $gerencianetRequest;
    private $gerencianetService;
    private $advertService;

    public function __construct()
    {
        $this->planService        = Factories::class(PlanService::class);
        $this->userService        = Factories::class(UserService::class);
        $this->gerencianetRequest = Factories::class(GerencianetRequest::class);
        $this->gerencianetService = Factories::class(GerencianetService::class);
        $this->advertService      = Factories::class(AdvertService::class);
    }

    public function index()
    {
        
        $advertsForHome = (object)$this->advertService->getAllAdvertsPaginated(perPage: 20);


        $data = [
            'title'   => 'anúncios recentes',
            'adverts' => $advertsForHome->adverts,
            'pager'   => $advertsForHome->pager
        ];

        return view('Web/Home/index', $data);
    }

    public function pricing()
    {
        $data = [
            'title' => 'Conheça os nossos planos',
            'plans' => $this->planService->getPlansToSell()
        ];

        return view('Web/Home/pricing', $data);
    }

    public function choice(int $planID = null)
    {
        
        if ($this->gerencianetService->userHasSubscription()) {

            return redirect()->route('dashboard')->with('info', 'Você já passui uma assinatura. Aproveite para cancelá-la e adquirir o novo Plano');
        }  

        if (!$this->userService->userDataIsComplete()) {

            // Usaremos para redirecionar após o user atualizar o perfil. Esse trecho é para o caso de o user ter logado antes de tentar comprar o Plano
            session()->set('choice', current_url());

            return redirect()->route('profile')->with('info', service('auth')->user()->fashMessageToUser());
        }

        $plan = $this->planService->getChoosenPlan($planID);

        $data = [
            'title' => "Realizar o pagamento do Plano {$plan->name}",
            'plan'  => $plan
        ];

        return view('Web/Home/choice', $data);
    }

    public function attemptPay(int $planID = null)
    {
        $this->gerencianetRequest->validateBeforeSave($this->request->getPost('payment_method'));

        $plan = $this->planService->getChoosenPlan($planID);

        $request = (object) $this->removeSpoofingFromRequest();
        
        if($request->payment_method == $this->gerencianetService::PAYMENT_METHOD_BILLET) {

            $qrcodeImage = $this->gerencianetService->createSubscription($plan, $request);

            $qrcodeImageBuilded = img(['src'=> $qrcodeImage, 'width' => '150px']);

            session()->setFlashdata('success', "Muito obrigado! Aproveite para realizar o pagamento do seu boleto Bancário com PIX <br/><br/>{$qrcodeImageBuilded}");
            
            return $this->response->setJSON($this->gerencianetRequest->respondWithMessage('Estamos aguardando a confirmação do pagamento'));
        }

        $this->gerencianetService->createSubscription($plan, $request);
        
        session()->setFlashdata('success', 'Muito obrigado! Estamos aguardando a confirmação do pagamento.');

        return $this->response->setJSON($this->gerencianetRequest->respondWithMessage('Estamos aguardando a confirmação do pagamento'));
    }

}
