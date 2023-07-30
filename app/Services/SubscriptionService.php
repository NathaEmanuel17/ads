<?php

namespace App\Services;

use App\Entities\Plan;
use App\Entities\Subscription;
use App\Models\SubscriptionModel;
use CodeIgniter\Config\Factories;
use stdClass;

class SubscriptionService
{
    private $subscriptionModel;
    private $user;

    public function __construct()
    {
        $this->subscriptionModel = Factories::models(SubscriptionModel::class);
        $this->user              = service('auth')->user();
    }

    public function tryInsertSubscription(Plan $choosenPlan, array $data): bool
    {

        // Criando objeto subscription da Entity
        $subscription = new Subscription();

        $subscription->user_id         = $this->user->id;
        $subscription->plan_id         = $choosenPlan->plan_id; // identificador do plano na gerencianet
        $subscription->subscription_id = $data['subscription_id']; // identificador da assinatura na gerencianet
        $subscription->status          = $data['status']; // identificador do status da assinatura na gerencianet

        $subscription->features = $this->planFeatures($choosenPlan);

        return $this->trySaveSubscription($subscription);
    }

    /**
     * Método que retorna as caracteristicas do plano que foi adquirido no momento da compra da assinatura
     * 
     * @param  Plan $choosenPlan
     * @return object
     */
    private function planFeatures(Plan $choosenPlan): object
    {
        $features = new stdClass;

        $features->id            = $choosenPlan->id;
        $features->plan_id       = $choosenPlan->plan_id;
        $features->name          = $choosenPlan->name;
        $features->value         = $choosenPlan->value;
        $features->value_details = $choosenPlan->details();
        $features->adverts       = $choosenPlan->adverts;
        
        return $features;
    } 

    public function trySaveSubscription(Subscription $subscription): bool
    {
        try {
            
            if($subscription->hasChanged()) {
                $this->subscriptionModel->saveSubscription($subscription);
            }

            return true;
        } catch (\Exception $e) {
            die('Não foi possivel pessistir a subscription');
        }
    }

    /**
     * Método que verifica se o usuario logado já possui assinatura
     * 
     * @return boolean
     */
    public function userHasSubscription(): bool
    {
        return $this->getUserSubscription() !== null; // se for diferente de nuçç, o user já possio assinatura
    }

    /**
     * Recupera a assinatura do usuário logado
     * 
     * @return object|null
     */
    public function getUserSubscription()
    {
        
        return $this->subscriptionModel->getUserSubscription();
    }

    public function tryDestroyUserSubscription(int $subscriptionID) 
    {
        try {
            return $this->subscriptionModel->destroyUserSubscription($subscriptionID);
        } catch (\Exception $w) {
            die('Não foi possivel destruir a assinatura');
        }
    }
}