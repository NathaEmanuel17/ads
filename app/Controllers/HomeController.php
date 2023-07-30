<?php

namespace App\Controllers;

use App\Requests\GerencianetRequest;
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

    public function __construct()
    {
        $this->planService        = Factories::class(PlanService::class);
        $this->userService        = Factories::class(UserService::class);
        $this->gerencianetRequest = Factories::class(GerencianetRequest::class);
        $this->gerencianetService = Factories::class(GerencianetService::class);
    }

    public function index()
    {
        $data = [
            'title' => 'anúncios recentes'
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

        /** 
         * @todo verifica se o user logado já tem assinatura
         */


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
        /*
            [payment_method] => credit
            [card_number] => 4881755992972070
            [card_expiration_date] => 2024-10-11
            [card_cvv] => 322
            [card_brand] => visa
            [zipcode] => 80530-000
            [street] => Avenida Cândido de Abreu
            [city] => Curitiba
            [neighborhood] => Centro Cívico
            [number] => 
            [state] => PR
            [expire_at] => 
            [payment_token] => 7c62edf2af38fc57a42f3569966453db172c97cf
        */

        $this->gerencianetRequest->validateBeforeSave($this->request->getPost('payment_method'));

        $plan = $this->planService->getChoosenPlan($planID);
        $request = (object) $this->removeSpoofingFromRequest();
        /**
         * @todo criar regra para capturar quando for boleto, pois queremos devolver para o anunciante o QRCODE
         */

        $this->gerencianetService->createSubscription($plan, $request);
        
        echo '<pre>';
        print_r($this->removeSpoofingFromRequest());
        exit;
    }
}
