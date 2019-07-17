<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $connection = 'retail';

    public static function ventas($fecha){
    	//return Venta::where('fecaltas', '=', $fecha)->get();
    	//return DB::connection('retail')->select("select * from ventas where fecaltas = '".$fecha."' limit 10");
    	return DB::connection('retail')
    	->table('ventas')
    	->join('sucursales', 'ID_SUCURSAL', '=', 'sucursales.CODIGO')
    	->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL, VENTAS.ID_SUCURSAL, sucursales.DESCRIPCION AS SUCURSAL'))
    	->where('VENTAS.FECALTAS', '=', $fecha)
    	->where('VENTAS.ID_SUCURSAL', '=', 4)
    	->groupBy('VENTAS.ID_SUCURSAL')
    	->get();
    }

    public static function generarConsulta($datos) {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursal = $datos['Sucursal'];
        
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        $ventasdet = DB::connection('retail')->table('ventasdet as v')
        ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
        ->join('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
        ->join('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
        DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
        DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
        DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
        DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
        DB::raw('v.COD_PROD'),
        DB::raw('PRODUCTOS.MARCA AS MARCA'),
        DB::raw('PRODUCTOS.LINEA AS LINEA'))  
        ->whereBetween('v.FECALTAS', [$inicio , $final])
        ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
        ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
        ])
        ->groupBy('v.COD_PROD')
        ->get()
        ->toArray(); 

        // $ventasdet = DB::connection('retail')->table('ventasdet as v')
        // ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
        // ->join('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
        // ->join('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        // ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
        // DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
        // DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
        // DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
        // DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
        // DB::raw('v.COD_PROD'),
        // DB::raw('PRODUCTOS.MARCA AS MARCA'),
        // DB::raw('PRODUCTOS.LINEA AS LINEA'))  
        // ->whereMonth('v.FECALTAS', 5)
        // ->where([
        //     ['v.ID_SUCURSAL', '=', 4],
        //     ['v.ANULADO', '<>', 1],
        //     ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
        // ])
        // ->groupBy('v.COD_PROD')
        // ->get()
        // ->toArray(); 

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = DB::connection('retail')->table('ventasdet as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereBetween('v.FECALTAS', [$inicio , $final])
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
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
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
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] += $value->PRECIO;
            } else {
                $categorias[$value->MARCA.''.$value->LINEA]["CODIGO"] = $value->LINEA;
                $categorias[$value->MARCA.''.$value->LINEA]["LINEA"] = $value->MARCA_NOMBRE.' '.$value->LINEA_NOMBRE;
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] = $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["MARCA"] = $value->MARCA;
            }

            /*  --------------------------------------------------------------------------------- */

        }

        $marca[] = (array) $marcas;
        $categoria[] = (array) $categorias;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $ventasdet, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];

        /*  --------------------------------------------------------------------------------- */
    }
}
