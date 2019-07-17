<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    
    public function index()
    {
    	$anio = date('Y');
    	$mes = date('m');
    	$ingresos = DB::connection('retail')->table('ventas as v')
    	->select(DB::raw('MONTH(v.FECALTAS) AS MES'), 
    	DB::raw('YEAR(v.FECALTAS) AS ANIO'),
    	DB::raw('SUM(v.TOTAL) AS TOTAL'))
    	->whereYear('v.FECALTAS', $anio)
    	->where('v.ID_SUCURSAL', '=', 4)
    	->groupBy(DB::raw('MONTH(v.FECALTAS)'), 
    	DB::raw('YEAR(v.FECALTAS)'))
    	->get();

    	$marcas = DB::connection('retail')->table('ventasdet as v')
    	->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
    	->join('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
    	->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
    	DB::raw('MARCA.DESCRIPCION AS MARCA'))	
    	->whereMonth('v.FECALTAS', 5)
    	->where([
		    ['v.ID_SUCURSAL', '=', 4],
		    ['v.ANULADO', '<>', 1],
		    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
		])
    	->groupBy(DB::raw('PRODUCTOS.MARCA'))
    	->get();

    	return ['ingresos' => $ingresos, 'marcas' => $marcas, 'anio' => $anio];
    }
}
