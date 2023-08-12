<?php

use App\Services\GerencianetService;
use CodeIgniter\Config\Factories;

if(!function_exists('reason_charge')) {

    function reason_charge(string $status): string
    {
        return Factories::class(GerencianetService::class)->reasonCharge($status);
    }
}

if(!function_exists('user_reached_adverts_limit')) {

    function user_reached_adverts_limit(): bool
    {
        return Factories::class(GerencianetService::class)->userReachedAdvertsLimit();
    }
}


if(!function_exists('count_all_user_adverts')) {

    function count_all_user_adverts(): int
    {
        return Factories::class(GerencianetService::class)->countAllUserAdverts();
    }
}