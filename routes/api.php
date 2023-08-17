<?php

$routes->group('api', ['namespace' => 'App\Controllers\API\V1'], static function ($routes) {

    $routes->post('login', 'JwtauthController::login');
    $routes->post('logout', 'JwtauthController::logout', ['filter' => 'auth:api']);
    $routes->post('refresh', 'JwtauthController::refresh', ['filter' => 'auth:api']);
    $routes->match(['get', 'post'], 'user', 'JwtauthController::user', ['filter' => 'auth:api']);

    // adverts do user autenticado através da API
    $routes->group('adverts', ['namespace' => 'App\Controllers\API\V1','filter' => 'subscription:api'], static function ($routes) {
        
       $routes->get('my', 'AdvertsUserController::index'); // Não usamos rotas nomeada
       $routes->get('my/(:num)', 'AdvertsUserController::getUserAdvert/$1'); // Não usamos rotas nomeada
       $routes->delete('my/(:num)', 'AdvertsUserController::deleteUserAdvert/$1'); // Não usamos rotas nomeada
       $routes->put('my/(:num)', 'AdvertsUserController::updateUserAdvert/$1'); // Não usamos rotas nomeada
       $routes->post('my/', 'AdvertsUserController::createUserAdvert'); // Não usamos rotas nomeada
    });
    

    $routes->resource('categories', ['only' => ['index'], 'controller' => 'CategoriesController', 'filter' => 'subscription:api']);   
});
