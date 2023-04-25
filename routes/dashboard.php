<?php

$routes->group('{locale}/dashboard', ['namespace' => 'App\Controllers\Dashboard', 'filter' => 'auth::web'], function ($routes) {

    $routes->get('/', 'DashboardController::index', ['filter' => 'verified', 'as' => 'dashboard']);

    // User ads
    /**
     * @todo criar filtro subscription
     */
    $routes->group('adverts', ['namespace' => 'App\Controllers\Dashboard'], function ($routes) {

        $routes->get('my', 'AdvertsUserController::index', ['as' => 'my.adverts']);
        $routes->get('get-all-my-adverts', 'AdvertsUserController::getUserAdverts', ['as' => 'get.all.my.Adverts']);
    });    
});
