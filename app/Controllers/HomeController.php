<?php

namespace App\Controllers;

use App\Services\PlanService;
use CodeIgniter\Config\Factories;

class HomeController extends BaseController
{

    private $planService;

    public function __construct()
    {
        $this->planService = Factories::class(PlanService::class);    
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

        $plan = $this->planService->getChoosenPlan($planID);
       
        $data = [
            'title' => "Realizar o pagamento do Plano {$plan->name}",
            'plan'  => $plan
        ];

        return view('Web/Home/choice', $data);
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function confirm()
    {
        return 'granted password';
    }
}
