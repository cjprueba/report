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

/* LARAVEL EXCEL */

use App\Exports\VentasMarca;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/download', function(){
	return Excel::download(new VentasMarca, 'ventasMarca.xlsx');
});

Route::post('/downloadVentaMarca', function(){


	return Excel::download(new VentasMarca(), 'ventasMarca.xlsx');
	//$myFile = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
	// $response =  array(
	//    'name' => "filename", //no extention needed
	//    'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($myFile) //mime type of used format
	// );

	// return response()->json($response);
});