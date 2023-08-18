<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Requests\UserRequest;
use App\Services\GerencianetService;
use App\Services\UserService;
use CodeIgniter\Config\Factories;

class DashboardController extends BaseController
{

    private $user;
    private $userRequest;
    private $userService;
    private $gerencianetService;
    
    public function __construct()
    {
        $this->user               = service('auth')->user();
        $this->userRequest        = Factories::class(UserRequest::class);
        $this->userService        = Factories::class(UserService::class);
        $this->gerencianetService = Factories::class(GerencianetService::class);
    }

    public function index()
    {
        $data = [
            'totalUSerAdverts'                => $this->gerencianetService->countAllUserAdverts(withDeleted: true),
            'totalPublishedAdverts'           => $this->gerencianetService->countAllUserAdverts(withDeleted: false, criteria: ['is_published' => true]),
            'totalUserAdvertsWaitingApproval' => $this->gerencianetService->countAllUserAdverts(withDeleted: true, criteria: ['is_published' => false]),
            'totalUserArchivedAdverts'        => $this->gerencianetService->countAllUserAdverts(withDeleted: true, criteria: ['deleted_at !=' => null]),
        ];

        return view('Dashboard/Home/index', $data);
    }
    
    public function myPlan()
    {

        $data = [
            'subscription' => $this->gerencianetService->getUserSubscription(),
            'hiddens'      => ['_method' => 'DELETE'], // Para o modal de cancelamento
        ];
        

        return view('Dashboard/Home/my_plan', $data);
    }

    public function profile()
    {
        
        $data = [
            'hiddens' => ['id' => $this->user->id, '_method' => 'PUT']
        ];

        return view('Dashboard/Home/profile', $data);
    }

    public function updateProfile()
    {

        $this->userRequest->validateBeforeSave('user_profile', respondWithRedirect: true);

        $this->userService->tryUpdateProfile($this->removeSpoofingFromRequest());

        if(session()->has('choice')) {
            return redirect()->to(session('choice'));
        }

        return redirect()->back()->with('success', lang('App.success_saved'));
    }

    public function access()
    {
        
        $data = [
            'hiddens' => ['id' => $this->user->id, '_method' => 'PUT']
        ];

        return view('Dashboard/Home/access', $data);
    }

    public function updateAccess()
    {
        $request = (object)$this->removeSpoofingFromRequest();

        if(!$this->userService->currentPasswordIsValid($request->current_password)) {

            return redirect()->back()->with('danger', 'Senha atual inválida');
        }

        $this->userRequest->validateBeforeSave('access_update', respondWithRedirect: true);

        $this->userService->tryUpdateAccess($request->password);

        return redirect()->back()->with('success', lang('App.success_saved'));
    }

    public function cancelSubscription()
    {       
        $this->gerencianetService->cancelSubscription();

        return redirect()->route('dashboard')->with('success', 'Sua assinatura foi cancelado com sucesso');

    }

    public function detailCharge(int $chargeID = null)
    {
        if(is_null($chargeID)) {

            return redirect()->back()->with('danger', 'Não identificamos a cobrança');
        }   

        $charge = $this->gerencianetService->detailCharge($chargeID);
        
        return redirect()->back()->with('charge', $charge);

    }

    public function confirmDeleteAccount()
    {
        $data = [
            'hiddens'      => ['_method' => 'DELETE'] 
        ];
        
        return view('Dashboard/Home/confirm_delete_account', $data);
    }

    public function accountDelete()
    {
        $this->userService->deleteUserAccout();

        return redirect()->route('web.home');
    }
}
