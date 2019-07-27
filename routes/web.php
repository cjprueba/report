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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);
/* Auth::routes(); */
 	


Route::get('/home', 'HomeController@index')->name('home');
Route::apiResource('categorias', 'CategoriaController');
Route::apiResource('ventas', 'VentaController');
Route::apiResource('charts', 'ChartController');
Route::apiResource('busquedas', 'BusquedaController');
Route::post('ventas', 'VentaController@mostrar');
Route::post('transferencias', 'TransferenciaControler@mostrar');