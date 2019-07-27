<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    public static function generarConsulta($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursalOrigen[] = $datos['SucursalOrigen'];
        $sucursalDestino = $datos['SucursalDestino'];
        $estatus = $datos['Estatus'];
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS TRANSFERENCIAS ENTRE LAS FECHAS INTERVALOS *********** */

        if ($datos['AllCategory'] AND $datos['AllBrand']) {

            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('t.CAMBIO'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))
            ->whereIn('t.SUCURSAL_ORIGEN', $datos['SucursalOrigen'])  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->where([
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray(); 

        } else if ($datos['AllCategory']) {
            
            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();

        } else if ($datos['AllBrand']) {
             
            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();
             
        } else  {

        	$transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();

        }


        /*  --------------------------------------------------------------------------------- */

        //  CAMBIAR A GUARANIES

        // foreach ($ventasdet as $key => $value) {
        //     if ($value->CAMBIO > 1) {
        //     	$ventasdet[$key] = $value->
        //     }
        // }

        /*  --------------------------------------------------------------------------------- */

        unset($marcas[0]);
        unset($categorias[0]);
        unset($totales[0]);

        /*  --------------------------------------------------------------------------------- */

        // CREAR FILA PARA PRODUCTOS CON MARCAS INDEFINIDAS

        // $marcas[0]["CODIGO"] = 0;
        // $marcas[0]["MARCA"] = "INDEFINIDO";
        // $marcas[0]["TOTAL"] = 0;

        /*  --------------------------------------------------------------------------------- */
        foreach ($transferencias_det as $key => $value) {


            /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE MARCAS

            if (array_key_exists($value->MARCA, $marcas))   {
                $marcas[$value->MARCA]["TOTAL"] += $value->PRECIO;
                $marcas[$value->MARCA]["STOCK"] += $value->STOCK;
                $marcas[$value->MARCA]["CANTIDAD"] += $value->CANTIDAD;
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["STOCK"] = $value->STOCK;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
                $marcas[$value->MARCA]["CANTIDAD"] = $value->CANTIDAD;
                $marcas[$value->MARCA]["STOCK_G"] = 0;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] += $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] += $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["CANTIDAD"] += $value->CANTIDAD;
            } else {
                $categorias[$value->MARCA.''.$value->LINEA]["CODIGO"] = $value->LINEA;
                $categorias[$value->MARCA.''.$value->LINEA]["LINEA"] = $value->LINEA_NOMBRE;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] = $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] = $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["MARCA"] = $value->MARCA;
                $categorias[$value->MARCA.''.$value->LINEA]["CANTIDAD"] = $value->CANTIDAD;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] = 0;
            }

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // BUSCAR STOCK GENERAL DE TODAS CATEGORIAS

        $stockGeneral = DB::connection('retail')
            ->table('LOTES as l')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.MARCA'),
            DB::raw('PRODUCTOS.LINEA'))
            ->where('l.ID_SUCURSAL', '=', $sucursalDestino)
            ->groupBy('PRODUCTOS.MARCA', 'PRODUCTOS.LINEA')
            ->get();

        foreach ($stockGeneral as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            if (array_key_exists($value->MARCA, $marcas))   {

                // CARGAR STOCK GENERAL A MARCA

                $marcas[$value->MARCA]["STOCK_G"] += $value->CANTIDAD;

            }

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {


            	// CARGAR STOCK GENERAL CATEGORIA

                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] += $value->CANTIDAD;
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        $marca[] = (array) $marcas;
        $categoria[] = (array) $categorias;

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $transferencias_det, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];

        /*  --------------------------------------------------------------------------------- */
    }
}
