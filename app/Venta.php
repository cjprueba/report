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

        //array_unshift($datos['Marcas'], 0);
        //var_dump($datos['Marcas']);
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        if ($datos['AllCategory'] AND $datos['AllBrand']) {
            $ventasdet = DB::connection('retail')->table('ventasdet as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

            
        } else if ($datos['AllCategory']) {
            
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
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

        } else if ($datos['AllBrand']) {
             
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
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 
        } else  {

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
        }

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

            if ($datos['AllCategory'] AND $datos['AllBrand']) {
                $ventas_con_descuentos = DB::connection('retail')->table('ventasdet as v')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else if ($datos['AllCategory']) { 

                 $ventas_con_descuentos = DB::connection('retail')->table('ventasdet as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else if ($datos['AllBrand']) { 

                 $ventas_con_descuentos = DB::connection('retail')->table('ventasdet as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else {

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

            }

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
            ->where('l.ID_SUCURSAL', '=', $sucursal)
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

        return ['ventas' => $ventasdet, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];

        /*  --------------------------------------------------------------------------------- */
    }

     public static function generarTablaMarca($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $mes = date('m', strtotime($datos['Inicio']));
        $anio = date('Y', strtotime($datos['Inicio']));
        $sucursal = $datos['Sucursal'];

        $total = 0;
        $totalVendido = 0;
        $totalStock = 0;


        // CARGAR MES PASADO

        if ($mes === 1) {
            $mes = 12;
            $anio = $anio - 1;
        } else {
            $mes = $mes - 1;
        }
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        

            $ventasdet = DB::connection('retail')->table('ventasdet as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('0 AS COMPORTAMIENTO_PRECIO'),
            DB::raw('0 AS COMPORTAMIENTO_VENDIDO'), 
            DB::raw('0 AS PRECIO_ANTERIOR'), 
            DB::raw('0 AS VENDIDO_ANTERIOR'), 
            DB::raw('0 AS P_TOTAL'),    
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('0 AS P_VENDIDO'),
            DB::raw('0 AS STOCK_G'),
            DB::raw('0 AS P_STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('PRODUCTOS.MARCA'))
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('PRODUCTOS.MARCA')
            ->get()
            ->toArray(); 

        /*  *********** MES ANTERIOR *********** */
            
            $ventasdetAnterior = DB::connection('retail')->table('ventasdet as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.MARCA'))
            ->whereMonth('v.FECALTAS', $mes)
            ->whereYear('v.FECALTAS', $anio)
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('PRODUCTOS.MARCA')
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
                ->select(DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'),
                DB::raw('PRODUCTOS.MARCA'))
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
                    $key = array_search($ventas_con_descuento->MARCA, array_column($ventasdet, 'MARCA'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES MES ANTERIOR *********** */

        $descuentos = DB::connection('retail')->table('ventasdet as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereMonth('v.FECALTAS', $mes)
        ->whereYear('v.FECALTAS', $anio)
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
                ->select(DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'),
                DB::raw('PRODUCTOS.MARCA'))
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
                    $key = array_search($ventas_con_descuento->MARCA, array_column($ventasdetAnterior, 'MARCA'));
                    $ventasdetAnterior[$key]->PRECIO = (int)$ventasdetAnterior[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        
        /*  --------------------------------------------------------------------------------- */
        // BUSCAR STOCK GENERAL DE TODAS CATEGORIAS

        // $stockGeneral = DB::connection('retail')
        //     ->table('LOTES as l')
        //     ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
        //     ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
        //     DB::raw('PRODUCTOS.MARCA'))
        //     ->where('l.ID_SUCURSAL', '=', $sucursal)
        //     ->groupBy('PRODUCTOS.MARCA')
        //     ->get();

        //return $stockGeneral;    
        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // $key2 = array_search($value->MARCA, array_column($ventasdet, 'MARCA'));
            // if ($key2 <> "null") {

            //     if (array_key_exists($key2, $ventasdet))   {
            //         $ventasdet[$key2]->STOCK_G += $value->CANTIDAD;   
            //     }
            // }

            $stockGeneral = DB::connection('retail')
            ->table('LOTES as l')
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.MARCA'))
            ->where('PRODUCTOS.MARCA', '=', $value->MARCA)
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('PRODUCTOS.MARCA')
            ->get();

            $ventasdet[$key]->STOCK_G = $stockGeneral[0]->CANTIDAD;

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // TOTALES

        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LA UBICACION DE LA MARCA EN LAS VENTAS ANTERIORES 

            $key2 = array_search($value->MARCA, array_column($ventasdetAnterior, 'MARCA'));
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR PRECIOS ANTERIORES

            if ($key2 <> null) {
                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO;
            } else if ($key2 === 0) {
                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO; 
            }

            /*  --------------------------------------------------------------------------------- */

            // CALCULAR COMPORTAMIENTOS 

            if ($ventasdet[$key]->PRECIO_ANTERIOR <> 0) {
                $ventasdet[$key]->COMPORTAMIENTO_PRECIO = number_format((($ventasdet[$key]->PRECIO / $ventasdet[$key]->PRECIO_ANTERIOR) - 1) * 100, 2);
            } else {
                $ventasdet[$key]->COMPORTAMIENTO_PRECIO = 100;
            }
            
            if ($ventasdet[$key]->VENDIDO_ANTERIOR <> 0) {

                $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = number_format((($ventasdet[$key]->VENDIDO / $ventasdet[$key]->VENDIDO_ANTERIOR) - 1) * 100, 2);
            } else {
                $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = 100;
            }
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR LOS TOTALES 

            $total += $value->PRECIO;
            $totalVendido += $value->VENDIDO;
            $totalStock += $value->STOCK_G;

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // CALCULAR LOS PORCENTAJES

        foreach ($ventasdet as $key => $value) {
            $ventasdet[$key]->P_TOTAL = round(($value->PRECIO * 100) / $total, 2);
            $ventasdet[$key]->P_VENDIDO = round(($value->VENDIDO * 100) / $totalVendido, 2);
            $ventasdet[$key]->P_STOCK = round(($value->STOCK_G * 100) / $totalStock, 2);
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['marcas' => $ventasdet];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function generarTablaCategoria($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $mes = date('m', strtotime($datos['Inicio']));
        $anio = date('Y', strtotime($datos['Inicio']));
        $sucursal = $datos['Sucursal'];

        $total = 0;
        $totalVendido = 0;
        $totalStock = 0;


        // CARGAR MES PASADO

        if ($mes === 1) {
            $mes = 12;
            $anio = $anio - 1;
        } else {
            $mes = $mes - 1;
        }
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        

            $ventasdet = DB::connection('retail')->table('ventasdet as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('0 AS COMPORTAMIENTO_PRECIO'),
            DB::raw('0 AS COMPORTAMIENTO_VENDIDO'), 
            DB::raw('0 AS PRECIO_ANTERIOR'), 
            DB::raw('0 AS VENDIDO_ANTERIOR'), 
            DB::raw('0 AS P_TOTAL'),    
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('0 AS P_VENDIDO'),
            DB::raw('0 AS STOCK_G'),
            DB::raw('0 AS P_STOCK'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('PRODUCTOS.LINEA'))
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('PRODUCTOS.LINEA')
            ->get()
            ->toArray(); 

        /*  *********** MES ANTERIOR *********** */
            
            $ventasdetAnterior = DB::connection('retail')->table('ventasdet as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('PRODUCTOS.LINEA'))
            ->whereMonth('v.FECALTAS', $mes)
            ->whereYear('v.FECALTAS', $anio)
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('PRODUCTOS.LINEA')
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
                ->select(DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'),
                DB::raw('PRODUCTOS.LINEA'))
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
                    $key = array_search($ventas_con_descuento->LINEA, array_column($ventasdet, 'LINEA'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES MES ANTERIOR *********** */

        $descuentos = DB::connection('retail')->table('ventasdet as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereMonth('v.FECALTAS', $mes)
        ->whereYear('v.FECALTAS', $anio)
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
                ->select(DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'),
                DB::raw('PRODUCTOS.LINEA'))
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
                    $key = array_search($ventas_con_descuento->LINEA, array_column($ventasdetAnterior, 'LINEA'));
                    $ventasdetAnterior[$key]->PRECIO = (int)$ventasdetAnterior[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        
        /*  --------------------------------------------------------------------------------- */
        // BUSCAR STOCK GENERAL DE TODAS CATEGORIAS

        // $stockGeneral = DB::connection('retail')
        //     ->table('LOTES as l')
        //     ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
        //     ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
        //     DB::raw('PRODUCTOS.MARCA'))
        //     ->where('l.ID_SUCURSAL', '=', $sucursal)
        //     ->groupBy('PRODUCTOS.MARCA')
        //     ->get();

        //return $stockGeneral;    
        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // $key2 = array_search($value->MARCA, array_column($ventasdet, 'MARCA'));
            // if ($key2 <> "null") {

            //     if (array_key_exists($key2, $ventasdet))   {
            //         $ventasdet[$key2]->STOCK_G += $value->CANTIDAD;   
            //     }
            // }

            $stockGeneral = DB::connection('retail')
            ->table('LOTES as l')
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.LINEA'))
            ->where('PRODUCTOS.LINEA', '=', $value->LINEA)
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('PRODUCTOS.LINEA')
            ->get();

            $ventasdet[$key]->STOCK_G = $stockGeneral[0]->CANTIDAD;

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // TOTALES

        // foreach ($ventasdetAnterior as $key => $value) {

        //     /*  --------------------------------------------------------------------------------- */

        //     // OBTENER LA UBICACION DE LA MARCA EN LAS VENTAS ANTERIORES 

        //     $key2 = array_search($value->LINEA, array_column($ventasdet, 'LINEA'));
            
        //     /*  --------------------------------------------------------------------------------- */

        //     // CARGAR PRECIOS ANTERIORES

        //     if ($key2 <> null) {
        //         $ventasdet[$key2]->PRECIO_ANTERIOR = $ventasdetAnterior[$key]->PRECIO;
        //         $ventasdet[$key2]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key]->VENDIDO;
        //     } else if ($key2 === 0) {
        //         $ventasdet[$key2]->PRECIO_ANTERIOR = $ventasdetAnterior[$key]->PRECIO;
        //         $ventasdet[$key2]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key]->VENDIDO; 
        //     } else {
        //          $ventasdet->append($ventasdet->PRECIO = 0);
        //         //                                             "COMPORTAMIENTO_PRECIO"=> 0, 
        //         //                                             "COMPORTAMIENTO_VENDIDO"=> 0, 
        //         //                                             "PRECIO_ANTERIOR"=> $ventasdetAnterior[$key]->PRECIO,
        //         //                                             "VENDIDO_ANTERIOR"=> $ventasdetAnterior[$key]->VENDIDO,
        //         //                                             "P_TOTAL"=> 0,
        //         //                                             "VENDIDO"=> 0,
        //         //                                             "P_VENDIDO"=> 0,
        //         //                                             "STOCK_G"=> 0,
        //         //                                             "P_STOCK"=> 0,
        //         //                                             "LINEA_NOMBRE"=> $ventasdetAnterior[$key]->LINEA_NOMBRE,
        //         //                                             "LINEA"=> $ventasdetAnterior[$key]->LINEA]);
        //         // $ventasdet->append("PRECIO"=> 0,
        //         //                                             "COMPORTAMIENTO_PRECIO"=> 0, 
        //         //                                             "COMPORTAMIENTO_VENDIDO"=> 0, 
        //         //                                             "PRECIO_ANTERIOR"=> $ventasdetAnterior[$key]->PRECIO,
        //         //                                             "VENDIDO_ANTERIOR"=> $ventasdetAnterior[$key]->VENDIDO,
        //         //                                             "P_TOTAL"=> 0,
        //         //                                             "VENDIDO"=> 0,
        //         //                                             "P_VENDIDO"=> 0,
        //         //                                             "STOCK_G"=> 0,
        //         //                                             "P_STOCK"=> 0,
        //         //                                             "LINEA_NOMBRE"=> $ventasdetAnterior[$key]->LINEA_NOMBRE,
        //         //                                             "LINEA"=> $ventasdetAnterior[$key]->LINEA);

        //     }

            
        // }

        // return $ventasdet;

        // foreach ($ventasdet as $key => $value) {

        //     /*  --------------------------------------------------------------------------------- */

        //     // CALCULAR COMPORTAMIENTOS 

        //     if ($ventasdet[$key]->PRECIO_ANTERIOR <> 0) {
        //         $ventasdet[$key]->COMPORTAMIENTO_PRECIO = number_format((($ventasdet[$key]->PRECIO / $ventasdet[$key]->PRECIO_ANTERIOR) - 1) * 100, 2);
        //     } else {
        //         $ventasdet[$key]->COMPORTAMIENTO_PRECIO = 100;
        //     }
            
        //     if ($ventasdet[$key]->VENDIDO_ANTERIOR <> 0) {

        //         $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = number_format((($ventasdet[$key]->VENDIDO / $ventasdet[$key]->VENDIDO_ANTERIOR) - 1) * 100, 2);
        //     } else {
        //         $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = 100;
        //     }
            
        //     /*  --------------------------------------------------------------------------------- */

        //     // CARGAR LOS TOTALES 

        //     $total += $value->PRECIO;
        //     $totalVendido += $value->VENDIDO;
        //     $totalStock += $value->STOCK_G;

        //     /*  --------------------------------------------------------------------------------- */
        // }

        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LA UBICACION DE LA MARCA EN LAS VENTAS ANTERIORES 

            $key2 = array_search($value->LINEA, array_column($ventasdetAnterior, 'LINEA'));
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR PRECIOS ANTERIORES

            if ($key2 <> null) {
                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO;
            } else if ($key2 === 0) {
                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO; 
            } 

            /*  --------------------------------------------------------------------------------- */

            // CALCULAR COMPORTAMIENTOS 

            if ($ventasdet[$key]->PRECIO_ANTERIOR <> 0) {
                $ventasdet[$key]->COMPORTAMIENTO_PRECIO = number_format((($ventasdet[$key]->PRECIO / $ventasdet[$key]->PRECIO_ANTERIOR) - 1) * 100, 2);
            } else {
                $ventasdet[$key]->COMPORTAMIENTO_PRECIO = 100;
            }
            
            if ($ventasdet[$key]->VENDIDO_ANTERIOR <> 0) {

                $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = number_format((($ventasdet[$key]->VENDIDO / $ventasdet[$key]->VENDIDO_ANTERIOR) - 1) * 100, 2);
            } else {
                $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = 100;
            }
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR LOS TOTALES 

            $total += $value->PRECIO;
            $totalVendido += $value->VENDIDO;
            $totalStock += $value->STOCK_G;

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // CALCULAR LOS PORCENTAJES

        foreach ($ventasdet as $key => $value) {
            $ventasdet[$key]->P_TOTAL = round(($value->PRECIO * 100) / $total, 2);
            $ventasdet[$key]->P_VENDIDO = round(($value->VENDIDO * 100) / $totalVendido, 2);
            $ventasdet[$key]->P_STOCK = round(($value->STOCK_G * 100) / $totalStock, 2);
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['categorias' => $ventasdet];

        /*  --------------------------------------------------------------------------------- */
    }
}
