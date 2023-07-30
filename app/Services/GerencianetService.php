<?php

namespace App\Services;

use Gerencianet\Exception\GerencianetException;
use Gerencianet\Gerencianet;
use App\Entities\Plan;

class GerencianetService
{
    public const PAYMENT_METHOD_BILLET = 'billet';
    public const PAYMENT_METHOD_CREDIT = 'credit';

    private const STATUS_NEW           = 'new';
    private const STATUS_WAITING       = 'waiting';
    private const STATUS_PAID          = 'paid';
    private const STATUS_UNPAID        = 'upaid';
    private const STATUS_REFUNDED      = 'refunded';
    private const STATUS_CONTESTED     = 'contested';
    private const STATUS_SETTLED       = 'settled';
    private const STATUS_CANCELED      = 'canceled';

    private $options;
    private $user;
    private $subscriptionService;
    private $userSubscription;

    public function __construct()
    {
        $this->options = [
            'client_id'        => env('GERENCIANET_CLIENT_ID'),
            'client_secret'    => env('GERENCIANET_CLIENT_SECRET'),
            'sandbox'          => env('GERENCIANET_SANDBOX'), // altere conforme o ambiente (true = Homologação e false = producao)
            'time'             => env('GERENCIANET_TIMEOUT')
        ];

        $this->user = service('auth')->user();
    }

    public function createPlan(Plan $plan)
    {
        // Definimos a periodicidade das cobranças a serem geradas
        $plan->setIntervalRepeats();

        $body = [
            'name'          => $plan->name,
            'interval'      => $plan->interval,
            'repeats'       => $plan->repeats
        ];

        try {
            $api = new Gerencianet($this->options);
            $response = $api->createPlan([], $body);

            $plan->plan_id = (int) $response['data']['plan_id'];

            //echo '<pre>' . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</pre>';
            //exit;
        } catch (GerencianetException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            die('Erro ao salvar plano na gerencianet');
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            die('Erro ao salvar plano na gerencianet');
        }
    }

    public function updatePlan(Plan $plan)
    {
        $params = ['id' => $plan->plan_id];

        $body = ['name' =>  $plan->name];

        try {
            $api = new Gerencianet($this->options);
            $response = $api->updatePlan($params, $body);

            // echo '<pre>' . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</pre>';
        } catch (GerencianetException $e) {

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            die('Erro ao salvar plano na gerencianet');
        } catch (\Exception $e) {

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            die('Erro ao salvar plano na gerencianet');
        }
    }

    public function deletePlan(int $planID)
    {
        $params = ['id' => $planID];

        try {
            $api = new Gerencianet($this->options);
            $response = $api->deletePlan($params, []);

            //echo '<pre>' . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</pre>';
        } catch (GerencianetException $e) {

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            die('Erro ao excluir plano na gerencianet');
        } catch (\Exception $e) {

            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            die('Erro ao excluir plano na gerencianet');
        }
    }

    //---------------------------------Gerenciamento de assinaturas---------------------------------//

    public function createSubscription(Plan $choosenPlan, object $request)
    {

        $params = ['id' => $choosenPlan->plan_id];

        $items = [
            [
                'name'   => $choosenPlan->name,
                'amount' => 1,

                // Valor (1000 = R$ 10,00) (Obs: é possivel a criação de itens com valore negativos. 
                //Porém , o valor total da fatura deve ser superior ao valor mínimo para geração de transações.)
                'value'  => (int) str_replace([',', '.'], '', $choosenPlan->value)
            ],
        ];

        $body = [
            'items' => $items
        ];

        try {

            /*
            boleto
            {
                "code": 200,
                "data": {
                    "subscription_id": 77008,
                    "status": "new",
                    "custom_id": null,
                    "charges": [
                        {
                            "charge_id": 43735351,
                            "status": "new",
                            "total": 19999,
                            "parcel": 1
                        }
                    ],
                    "created_at": "2023-07-29 20:43:20"
                }
            }

            cartão
            {
                "code": 200,
                "data": {
                    "subscription_id": 77025,
                    "status": "active",
                    "plan": {
                        "id": 10287,
                        "interval": 1,
                        "repeats": null
                    },
                    "charge": {
                        "id": 43735371,
                        "status": "waiting",
                        "parcel": 1,
                        "total": 3990
                    },
                    "first_execution": "29/07/2023",
                    "total": 3990,
                    "payment": "credit_card"
                }
            }
            */
            
            $api = new Gerencianet($this->options);
            $response = $api->createSubscription($params, $body);

            $choosenPlan->subscription_id = (int) $response['data']['subscription_id'];

            /**
             * @todo avaliar quando for boleto para obtermos o QRCODE da gerencianet
             */

            // $this->paySubscription($choosenPlan, $request);
            $this->paySubscription($choosenPlan, $request);

            //echo '<pre>' . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</pre>';
            //exit;
        } catch (GerencianetException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            die('Erro ao criar assinatura na gerencianet');
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            die('Erro ao criar assinatura na gerencianet');
        }
    }

    private function paySubscription(Plan $choosenPlan, object $request)
    {
    
        $params = ['id' => $choosenPlan->subscription_id];

        $customer = [
            'name'          => $this->user->fullname(),
            'cpf'           => str_replace(['.', '-'], '', $this->user->cpf),
            'phone_number'  => str_replace(['(', ')', ' ', '-'], '', $this->user->phone),
            'email'         => $this->user->email,
            'birth'         => $this->user->birth
        ];

        $billingAddress = [
            'street'       => $request->street,
            'number'       => ($request->number ? (int) $request->number : 'Não informado'),
            'neighborhood' => $request->neighborhood,
            'zipcode'      => str_replace(['-'], '', $request->zipcode),
            'city'         => $request->city,
            'state'        => $request->state,
        ];

        // É boleto?
        if ($request->payment_method === self::PAYMENT_METHOD_BILLET) {

            // Sim...
            $body = [
                'payment' => [
                    'banking_billet' => [
                        'expire_at'  => $request->expire_at,
                        'customer'   => $customer
                    ]
                ]
            ];
        } else {
            // Não... é cartão de credito
            $body = [
                'payment' => [
                    'credit_card' => [
                        'billing_address' => $billingAddress,
                        'payment_token'   => $request->payment_token,
                        'customer'        => $customer
                    ]
                ]
            ];
        }



        try {
            /*
            {
                "code": 200,
                "data": {
                    "subscription_id": 77022,
                    "status": "active",
                    "barcode": "00000.00000 00000.000000 00000.000000 0 00000000000000",
                    "pix": {
                        "qrcode": "Este QRCode não pode ser pago, ele foi gerado em ambiente sandbox da Gerencianet.",
                        "qrcode_image": "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMyAzMyIgc2hhcGUtcmVuZGVyaW5nPSJjcmlzcEVkZ2VzIj48cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNMCAwaDMzdjMzSDB6Ii8+PHBhdGggc3Ryb2tlPSIjMDAwMDAwIiBkPSJNMCAwLjVoN200IDBoMm0xIDBoMW00IDBoMW0yIDBoMW0xIDBoMW0xIDBoN00wIDEuNWgxbTUgMGgxbTUgMGgxbTMgMGgzbTEgMGgxbTMgMGgxbTEgMGgxbTUgMGgxTTAgMi41aDFtMSAwaDNtMSAwaDFtMSAwaDJtMSAwaDJtMiAwaDJtMSAwaDRtMSAwaDJtMSAwaDFtMSAwaDNtMSAwaDFNMCAzLjVoMW0xIDBoM20xIDBoMW0xIDBoMW00IDBoMW0zIDBoMW0zIDBoMW0xIDBoMW0yIDBoMW0xIDBoM20xIDBoMU0wIDQuNWgxbTEgMGgzbTEgMGgxbTQgMGgxbTEgMGgxbTMgMGgybTEgMGgxbTIgMGgybTEgMGgxbTEgMGgzbTEgMGgxTTAgNS41aDFtNSAwaDFtMiAwaDFtMiAwaDZtMyAwaDFtMSAwaDFtMiAwaDFtNSAwaDFNMCA2LjVoN20xIDBoMW0xIDBoMW0xIDBoMW0xIDBoMW0xIDBoMW0xIDBoMW0xIDBoMW0xIDBoMW0xIDBoMW0xIDBoN005IDcuNWgxbTEgMGgxbTEgMGgxbTEgMGgybTEgMGgybTQgMGgxTTMgOC41aDJtMSAwaDJtMSAwaDJtMSAwaDJtMSAwaDFtNCAwaDFtMyAwaDFtNCAwaDJNNSA5LjVoMW0yIDBoMm0zIDBoMm0xIDBoMW0xIDBoMW0yIDBoNW0xIDBoNE0wIDEwLjVoMW0xIDBoNm0xIDBoMW0xIDBoMm0yIDBoMm0yIDBoMm0xIDBoMm0xIDBoM20yIDBoM00wIDExLjVoMW0zIDBoMm0xIDBoMW0yIDBoMW0yIDBoMm0xIDBoMW0xIDBoMm0xIDBoM20xIDBoMW0xIDBoMm0xIDBoM00wIDEyLjVoNG0yIDBoMW0xIDBoMm0xIDBoMW0zIDBoMm00IDBoMW0yIDBoMW0xIDBoM20zIDBoMU0wIDEzLjVoM200IDBoMW0yIDBoMW0xIDBoMm0xIDBoMW0xIDBoMW0zIDBoMW0xIDBoMW0xIDBoMW0xIDBoM20xIDBoMU0zIDE0LjVoNW0xIDBoMm0xIDBoMW0xIDBoMW01IDBoMW00IDBoMW0xIDBoMU0wIDE1LjVoMW0yIDBoMm0yIDBoMW00IDBoMm0xIDBoMm0xIDBoMW00IDBoM20xIDBoMW0xIDBoMU0wIDE2LjVoMW0xIDBoMW0yIDBoMm0xIDBoMm0yIDBoMm0xIDBoMm0xIDBoMW0xIDBoMW0yIDBoMW0xIDBoNk0wIDE3LjVoM20xIDBoMm01IDBoMm0yIDBoMW0zIDBoMm0xIDBoMW0xIDBoMW0xIDBoNE0yIDE4LjVoMW0xIDBoM203IDBoMW0xIDBoMm0yIDBoNG0xIDBoM20xIDBoNE0yIDE5LjVoMW0xIDBoMW0yIDBoMm0xIDBoMW0xIDBoMW0xIDBoMW0xIDBoNG0yIDBoM20xIDBoNk0wIDIwLjVoMm0xIDBoMm0xIDBoMW0xIDBoMm0xIDBoM201IDBoMW00IDBoMm0xIDBoMW0xIDBoMW0xIDBoMU0wIDIxLjVoMm0xIDBoMm0yIDBoMm0xIDBoMW0yIDBoMW0xIDBoMm0xIDBoM20xIDBoMW0yIDBoNk0wIDIyLjVoMW0xIDBoMW0xIDBoM20yIDBoNm0yIDBoMW0xIDBoMm0zIDBoMW0xIDBoM20xIDBoM00wIDIzLjVoMW0yIDBoMW0xIDBoMW02IDBoMm0yIDBoMW0xIDBoMW0xIDBoMW0xIDBoMW01IDBoMW0xIDBoMW0xIDBoMU0wIDI0LjVoMm00IDBoMW02IDBoMW0xIDBoMW0xIDBoNG0zIDBoNU04IDI1LjVoM20xIDBoMm0xIDBoMW0xIDBoMW0xIDBoMm0xIDBoMW0xIDBoMW0zIDBoNE0wIDI2LjVoN20xIDBoNG0xIDBoMm0xIDBoMW0xIDBoMW0yIDBoNG0xIDBoMW0xIDBoMW0xIDBoMU0wIDI3LjVoMW01IDBoMW0yIDBoMW02IDBoM20xIDBoMW0yIDBoMm0zIDBoMW0xIDBoMk0wIDI4LjVoMW0xIDBoM20xIDBoMW0xIDBoMW0zIDBoMW0zIDBoMm0zIDBoMW0xIDBoN20xIDBoMU0wIDI5LjVoMW0xIDBoM20xIDBoMW0xIDBoMW0yIDBoMW0zIDBoMW0zIDBoM20zIDBoMW0xIDBoMW0xIDBoM00wIDMwLjVoMW0xIDBoM20xIDBoMW0zIDBoMW0xIDBoM20yIDBoMm0xIDBoMW03IDBoMW0xIDBoM00wIDMxLjVoMW01IDBoMW0zIDBoM20yIDBoM20zIDBoMW04IDBoM00wIDMyLjVoN20yIDBoMW0yIDBoMW0yIDBoMm0yIDBoMW0xIDBoMW0xIDBoNG0xIDBoMyIvPjwvc3ZnPg=="
                    },
                    "link": "https://download.gerencianet.com.br/v1/417665_1_PARAA5/417665-1-HIDO3?sandbox=true",
                    "billet_link": "https://visualizacaosandbox.gerencianet.com.br/emissao/417665_1_PARAA5/A4XB-417665-1-HIDO3",
                    "pdf": {
                        "charge": "https://download.gerencianet.com.br/417665_1_PARAA5/417665-1-HIDO3.pdf?sandbox=true"
                    },
                    "expire_at": "2023-07-31",
                    "plan": {
                        "id": 10289,
                        "interval": 6,
                        "repeats": null
                    },
                    "charge": {
                        "id": 43735368,
                        "status": "waiting",
                        "parcel": 1,
                        "total": 19999
                    },
                    "first_execution": "29/07/2023",
                    "total": 19999,
                    "payment": "banking_billet"
                }
            }
            */
            $api = new Gerencianet($this->options);

            $response = $api->paySubscription($params, $body);

            echo '<pre>' . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</pre>';
            exit;
        } catch (GerencianetException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            
            die('Erro ao pagar assinatura na gerencianet');
        } catch (\Exception $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);

            die('Erro ao pagar assinatura na gerencianet');
        }
    }
}
