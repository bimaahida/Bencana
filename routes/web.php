<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// $router->get('/', function () use ($router) {
//     return view('welcome');
// });
$router->group(['prefix' => ''],function() use ($router){
    $router->get('/',  ['uses' => 'BencanaController@maps']);
});

$router->group(['prefix' => 'bencana'],function() use ($router){
    $router->get('/',  ['uses' => 'BencanaController@index']);
    $router->post('/importAction',  ['uses' => 'BencanaController@import']);
});
