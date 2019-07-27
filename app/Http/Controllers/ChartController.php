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

        $marcas[] = array();
        $categorias[] = array();
        $sucursal = 4;

    	$ingresos = DB::connection('retail')->table('ventas as v')
    	->select(DB::raw('MONTH(v.FECALTAS) AS MES'), 
    	DB::raw('YEAR(v.FECALTAS) AS ANIO'),
    	DB::raw('SUM(v.TOTAL) AS TOTAL'))
    	->whereYear('v.FECALTAS', $anio)
    	->where('v.ID_SUCURSAL', '=', 4)
    	->groupBy(DB::raw('MONTH(v.FECALTAS)'), 
    	DB::raw('YEAR(v.FECALTAS)'))
    	->get();

  //   	$marcas = DB::connection('retail')->table('ventasdet as v')
  //   	->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
  //   	->join('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
  //   	->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
  //   	DB::raw('MARCA.DESCRIPCION AS MARCA'))	
  //   	->whereMonth('v.FECALTAS', 5)
  //   	->where([
		//     ['v.ID_SUCURSAL', '=', 4],
		//     ['v.ANULADO', '<>', 1],
		//     ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
		// ])
  //   	->groupBy(DB::raw('PRODUCTOS.MARCA'))
  //   	->get();

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        $ventasdet = DB::connection('retail')->table('ventasdet as v')
        ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
        ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
        ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
        DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
        DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
        DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
        DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
        DB::raw('v.COD_PROD'),
        DB::raw('PRODUCTOS.MARCA AS MARCA'),
        DB::raw('PRODUCTOS.LINEA AS LINEA'))  
        ->whereMonth('v.FECALTAS', $mes)
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
        ])
        ->groupBy('v.COD_PROD')
        ->get()
        ->toArray(); 

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = DB::connection('retail')->table('ventasdet as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
       ->whereMonth('v.FECALTAS', $mes)
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['v.COD_PROD', '=', 2],
        ])
        ->get(); 

        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  --------------------------------------------------------------------------------- */

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            $ventas_con_descuentos = DB::connection('retail')->table('ventasdet as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->select(DB::raw('v.COD_PROD'),
            DB::raw('v.PRECIO'),
            DB::raw('v.PRECIO_UNIT'),
            DB::raw('v.ITEM'))  
            ->whereMonth('v.FECALTAS', 6)
            ->where([
                ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                ['v.CODIGO', '=', $descuento->CODIGO],
                ['v.CAJA', '=', $descuento->CAJA],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->get();

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {
                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {
                    $key = array_search($ventas_con_descuento->COD_PROD, array_column($ventasdet, 'COD_PROD'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        unset($marcas[0]);
        unset($categorias[0]);

        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE MARCAS

            if (array_key_exists($value->MARCA, $marcas))   {
                $marcas[$value->MARCA]["TOTAL"] += $value->PRECIO;
                $marcas[$value->MARCA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
                $marcas[$value->MARCA]["VENDIDO"] = $value->VENDIDO;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->LINEA, $categorias))   {
                $categorias[$value->LINEA]["TOTAL"] += $value->PRECIO;
                $categorias[$value->LINEA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $categorias[$value->LINEA]["CODIGO"] = $value->LINEA;
                $categorias[$value->LINEA]["LINEA"] = $value->LINEA_NOMBRE;
                $categorias[$value->LINEA]["TOTAL"] = $value->PRECIO;
                $categorias[$value->LINEA]["VENDIDO"] = $value->VENDIDO;
            }

            /*  --------------------------------------------------------------------------------- */

        }

        $marca[] = (array) $marcas;
        $categoria[] = (array) $categorias;

    	return ['ingresos' => $ingresos, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0], 'anio' => $anio];
    }
}
