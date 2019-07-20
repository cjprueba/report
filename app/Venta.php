<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $connection = 'retail';

    public static function ventas($fecha)
    {
    	
        /*  --------------------------------------------------------------------------------- */

        //INIICAR VARIABLES

        $anioNull[] = array("TOTAL" => 0,"ID_SUCURSAL"=>0,"SUCURSAL"=>"Ninguna Venta");
        $anio = date('Y');
        $mes = date('m');
        $dia = date('d');
        $sucursal = 4;
        $ventasMes[] = array();

        /*  --------------------------------------------------------------------------------- */

        // CALCULAR DATOS DE FECHAS

        if($mes === 1) {
            $mesAnterior = 12;
            $anioAnterior = $anio - 1;
        } else {
            $mesAnterior = $mes - 1;
            $anioAnterior = $anio;
        }

        if($dia === 1) {
            if ($mes === 1) {
                $diaAnterior = date("d",(mktime(0,0,0,$mes+1,1,$anio-1)-1));
                $diaMesAnterior = 12;
                $diaAnioAnterior = $anio - 1;
            } else {
                $diaAnterior = date("d",(mktime(0,0,0,$mes,1,$anio)-1));
                $diaMesAnterior = $mes - 1;
                $diaAnioAnterior = $anio;
            }
        } else {
            $diaAnterior = $dia - 1;
            $diaMesAnterior = $mes;
            $diaAnioAnterior = $anio;
        }
        
        //print_r("dia Anterior ".$diaAnterior." Mes Anterior ".$diaMesAnterior." Año anteriro ".$diaAnioAnterior);
        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - PRIMERA CAJA
        
        // Año y dia Actual

        $diaActualR = DB::connection('retail')
        ->table('ventas')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anio)
        ->whereMonth('VENTAS.FECALTAS', $mes)
        ->whereDay('VENTAS.FECALTAS', $dia)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        // Año y mes Anterior
        
        $diaAnteriorR = DB::connection('retail')
        ->table('ventas')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $diaAnioAnterior)
        ->whereMonth('VENTAS.FECALTAS', $diaMesAnterior)
        ->whereDay('VENTAS.FECALTAS', $diaAnterior)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - SEGUNDA CAJA
    	
        // Año y mes Actual

        $mesActualR = DB::connection('retail')
    	->table('ventas')
    	->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
    	->whereYear('VENTAS.FECALTAS', $anio)
        ->whereMonth('VENTAS.FECALTAS', $mes)
    	->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
    	->groupBy('VENTAS.ID_SUCURSAL')
    	->get();

        // Año y mes Anterior
        
        $mesAnteriorR = DB::connection('retail')
        ->table('ventas')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anioAnterior)
        ->whereMonth('VENTAS.FECALTAS', $mesAnterior)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - TERCERA CAJA
        
        // Año Actual

        $anioActualR = DB::connection('retail')
        ->table('ventas')
        ->join('sucursales', 'ID_SUCURSAL', '=', 'sucursales.CODIGO')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL, VENTAS.ID_SUCURSAL, sucursales.DESCRIPCION AS SUCURSAL'))
        ->whereYear('VENTAS.FECALTAS', $anio)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        // Año Anterior
        
        $anioAnteriorR = DB::connection('retail')
        ->table('ventas')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anio - 1)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CUARTA CAJA
        
        // CANTIDAD DE ANULACIONES

        $anulacionesActual = DB::connection('retail')
        ->table('ventasdet')
        ->select(DB::raw('COUNT(ANULADO) AS ANULADO'))
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->where('VENTASDET.ID_SUCURSAL', '=', $sucursal)
        ->where('VENTASDET.ANULADO', '=', 1)
        ->groupBy('VENTASDET.CODIGO')
        ->groupBy('VENTASDET.CAJA')
        ->get();


        /*  --------------------------------------------------------------------------------- */

        $anulacionesActualTotal = DB::connection('retail')
        ->table('ventasdet')
        ->select(DB::raw('SUM(PRECIO) AS TOTAL'))
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->where('VENTASDET.ID_SUCURSAL', '=', $sucursal)
        ->where('VENTASDET.ANULADO', '=', 1)
        ->where(function ($query) {
            $query->where('VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%')
            ->where('VENTASDET.COD_PROD', '<>', 2);
        })
        ->groupBy('VENTASDET.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        //  PREPARAR ARRAY

        unset($ventasMes[0]);

        // MES ACTUAL
        
        if (count($mesActualR) <> 0) {
            foreach ($mesActualR as $value) {
                $ventasMes[0]["mesActual"] = $value->TOTAL;
            }
        } else {
            $ventasMes[0]["mesActual"] = 0;
        }  
         
        if (count($mesAnteriorR) <> 0) {    
            foreach ($mesAnteriorR as $value) {
                $ventasMes[0]["mesAnterior"] = $value->TOTAL;
                $ventasMes[0]["comportamiento"] = number_format((($ventasMes[0]["mesActual"] / $ventasMes[0]["mesAnterior"]) - 1) * 100, 2);
            }
        } else {
            $ventasMes[0]["mesAnterior"] = 0;
            if ($ventasMes[0]["mesActual"] === 0) {
                $ventasMes[0]["comportamiento"] = 0;
            } else {
                $ventasMes[0]["comportamiento"] = 100;
            }
        } 

        // DIA ACTUAL

        if (count($diaActualR) <> 0) {
            foreach ($diaActualR as $key => $value) {
                $ventasMes[0]["diaActual"] = $value->TOTAL;
            }
        } else {
            $ventasMes[0]["diaActual"] = 0;
        }    

        // DIA ANTERIOR

        if (count($diaAnteriorR) <> 0) {
            foreach ($diaAnteriorR as $key => $value) {
                $ventasMes[0]["diaAnterior"] = $value->TOTAL;
                $ventasMes[0]["comportamientoDia"] = number_format((($ventasMes[0]["diaActual"] / $ventasMes[0]["diaAnterior"]) - 1) * 100, 2);
            }
        } else {
            $ventasMes[0]["diaAnterior"] = 0;
            if ($ventasMes[0]["diaActual"] === 0) {
                $ventasMes[0]["comportamientoDia"] = 0;
            } else {
                $ventasMes[0]["comportamientoDia"] = 100;
            }
        } 

        // AÑO ACTUAL

        if (count($anioActualR) <> 0) {
            foreach ($anioActualR as $key => $value) {
                $ventasMes[0]["anioActual"] = $value->TOTAL;
                $ventasMes[0]["sucursal"] = $value->SUCURSAL;
                $ventasMes[0]["id_sucursal"] = $value->ID_SUCURSAL; 
            }
        } else {
            $ventasMes[0]["anioActual"] = 0;
        }    

        // AÑO ANTERIOR

        if (count($anioAnteriorR) <> 0) {
            foreach ($anioAnteriorR as $key => $value) {
                $ventasMes[0]["anioAnterior"] = $value->TOTAL;
                $ventasMes[0]["comportamientoAnio"] = number_format((($ventasMes[0]["anioActual"] / $ventasMes[0]["anioAnterior"]) - 1) * 100, 2);
            }
        } else {
            $ventasMes[0]["anioAnterior"] = 0;
            if ($ventasMes[0]["anioActual"] === 0) {
                $ventasMes[0]["comportamientoAnio"] = 0;
            } else {
                $ventasMes[0]["comportamientoAnio"] = 100;
            }
        } 

        // ANULACIONES CANTIDAD

        if (count($anulacionesActual) <> 0) {
            $ventasMes[0]["anulado"] = 0;
            foreach ($anulacionesActual as $value) {
                $ventasMes[0]["anulado"] += 1;
            }
        } else {
            $ventasMes[0]["anulado"] = 0;
        }

        // ANULACIONES TOTAL

        if (count($anulacionesActualTotal) <> 0) {
            foreach ($anulacionesActualTotal as $value) {
                $ventasMes[0]["anuladoTotal"] = -$value->TOTAL;
            }
        } else {
            $ventasMes[0]["anuladoTotal"] = 0;
        }
            
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        if (count($ventasMes) === 0) {
            return $anioNull;
        } else {
            return $ventasMes;
        }

        /*  --------------------------------------------------------------------------------- */
        
    }

    public static function generarConsulta($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursal = $datos['Sucursal'];

        // CARGAR MARCAS 0 EN VENTAS

        array_unshift($datos['Marcas'], 0);
        //var_dump($datos['Marcas']);
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        $ventasdet = DB::connection('retail')->table('ventasdet as v')
        ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
        ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
        ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
        DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
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
        unset($totales[0]);

        /*  --------------------------------------------------------------------------------- */

        // CREAR FILA PARA PRODUCTOS CON MARCAS INDEFINIDAS

        // $marcas[0]["CODIGO"] = 0;
        // $marcas[0]["MARCA"] = "INDEFINIDO";
        // $marcas[0]["TOTAL"] = 0;

        /*  --------------------------------------------------------------------------------- */
        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE MARCAS

            if (array_key_exists($value->MARCA, $marcas))   {
                $marcas[$value->MARCA]["TOTAL"] += $value->PRECIO;
                $marcas[$value->MARCA]["STOCK"] += $value->STOCK;
                $marcas[$value->MARCA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["STOCK"] = $value->STOCK;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
                $marcas[$value->MARCA]["VENDIDO"] = $value->VENDIDO;
                $marcas[$value->MARCA]["STOCK_G"] = 0;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] += $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] += $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $categorias[$value->MARCA.''.$value->LINEA]["CODIGO"] = $value->LINEA;
                $categorias[$value->MARCA.''.$value->LINEA]["LINEA"] = $value->LINEA_NOMBRE;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] = $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] = $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["MARCA"] = $value->MARCA;
                $categorias[$value->MARCA.''.$value->LINEA]["VENDIDO"] = $value->VENDIDO;
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
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('l.COD_PROD')
            ->get();

        foreach ($stockGeneral as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // CARGAR STOCK GENERAL A MARCA

            if (array_key_exists($value->MARCA, $marcas))   {
                $marcas[$value->MARCA]["STOCK_G"] += $value->CANTIDAD;
            }

             /*  --------------------------------------------------------------------------------- */

            // CARGAR STOCK GENERAL CATEGORIA

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] += $value->CANTIDAD;
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        $marca[] = (array) $marcas;
        $categoria[] = (array) $categorias;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $ventasdet, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];

        /*  --------------------------------------------------------------------------------- */
    }
}
