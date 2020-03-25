<?php

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

$router->options('/{any:.*}', ['middleware' => 'cors', function() {
    return response(['status' => 'success']);
  }]);

/**
* NCOV Special
*/
$router->group(['prefix' => 'api/ncov', 'middleware' => 'cors'], function($router)
{
    $router->get('all', ['uses' => 'ScrapController@getNVOCAll']);
    $router->get('country/{country}', ['uses' => 'ScrapController@getNCOVByCountry']);
    $router->get('countrylist', ['uses' => 'ScrapController@getNCOVAllCountry']);
});
