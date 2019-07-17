<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusquedaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /*  *********** SUCURSALES *********** */

        $sucursales = DB::connection('retail')->table('sucursales')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('CODIGO')
        ->get();

        /*  *********** MARCAS *********** */

        $marcas = DB::connection('retail')->table('marca')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('CODIGO')
        ->get();

        /*  *********** CATEGORIAS *********** */

        $categorias = DB::connection('retail')->table('lineas')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('CODIGO')
        ->get();

        /*  *********** RETORNAR VALORES *********** */

        return ['sucursales' => $sucursales, 'marcas' => $marcas, 'categorias' => $categorias];
    }

    
}
