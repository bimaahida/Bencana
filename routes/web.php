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
    $router->get('/',  ['uses' => 'BencanaController@index','as' => 'bencana.index']);
    $router->post('/importAction',  ['uses' => 'BencanaController@import','as' => 'bencana.importaction']);
    $router->get('/loadData',  ['uses' => 'BencanaController@loadData','as' => 'bencana.loadData']);
});

$router->group(['prefix' => 'wilayah'],function() use ($router){
    $router->get('/datatables',['uses' => 'WilayahController@dataTables','as' => 'wilayah.datatables']);
    $router->get('/create',  ['uses' => 'WilayahController@create','as'=>'wilayah.create']);
    $router->get('/datatablesDetail/{id}',['uses' => 'WilayahController@dataTablesDetail','as' => 'wilayah.datatablesDetail']);
    $router->get('/show/{id}',  ['uses' => 'WilayahController@show','as'=>'wilayah.show']);
    $router->get('/{id}',  ['uses' => 'WilayahController@edit','as' => 'wilayah.edit']);
    $router->put('/{id}',  ['uses' => 'WilayahController@update','as' =>'wilayah.update']);
    $router->delete('/{id}',  ['uses' => 'WilayahController@destroy','as' => 'wilayah.destroy']);
    $router->post('/',  ['uses' => 'WilayahController@store','as'=>'wilayah.store']);
    $router->get('/',  ['uses' => 'WilayahController@index','as'=>'wilayah.index']);
});

$router->group(['prefix' => 'position'],function() use ($router){
    $router->post('/',  ['uses' => 'LatlongController@store','as' => 'position.store']);
    $router->delete('/{id}/{area}',  ['uses' => 'LatlongController@destroy','as' => 'position.destroy']);
    $router->get('/loadData',  ['uses' => 'LatlongController@loadData','as' => 'position.loadData']);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

