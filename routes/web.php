<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');

    $router->post('/logout', [
        'middleware' => 'auth',
        'uses' => 'AuthController@logout'
    ]);
});

$router->group(['middleware' => 'auth', 'prefix' => 'api'], function () use ($router) {
    $router->get('/categories', 'CategoryController@index');
    $router->post('/categories', 'CategoryController@create');
    $router->get('/categories/{id}', 'CategoryController@show');
    $router->patch('/categories/{id}', 'CategoryController@update');
    $router->delete('/categories/{id}', 'CategoryController@delete');

    $router->get('/products', 'ProductController@index');
    $router->post('/products', 'ProductController@create');
    $router->get('/products/{id}', 'ProductController@show');
    $router->patch('/products/{id}', 'ProductController@update');
    $router->delete('/products/{id}', 'ProductController@delete');
});