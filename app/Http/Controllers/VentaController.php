<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venta;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VentaController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return Venta::whereDate('FECALTAS', '2019-06-01');
        //return DB::connection('retail')->select('select * from ventas limit 10');
        $ventas = Venta::ventas(date("Y-m-d"));
        return response()->json($ventas);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($datos)
    {
        //$ventas = Venta::generarConsulta($datos);
        //var_dump($datos);

    }

    public function mostrar(Request $request)
    {
        if ($request["Opcion"] === 2) {
            $ventas = Venta::generarTablaMarca($request->all());
            return response()->json($ventas);
        } else if  ($request["Opcion"] === 3) {
            $ventas = Venta::generarTablaCategoria($request->all());
            return response()->json($ventas);
        } else {
            $ventas = Venta::generarConsulta($request->all());
            return response()->json($ventas);
        }
        //return response()->json([$request->all()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
