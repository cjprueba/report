<?php

namespace App\Exports;

use App\Ventas_det;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;

class VentasMarca implements WithMultipleSheets
{
  private $marca;
  private $descuentogeneral;
  private $descuento;
  private $calculo;
  private $calculos;
  private $ventageneral;
  private $linea;
  private $nullsheets;
  private $sheets = [];
  private $hojas=1;
  private $inicio;
  private $final;
  private $sucursal;
         use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
 public function __construct(Request $request)
    {
        $datos = $request->all();
        $this->inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $this->final  =  date('Y-m-d', strtotime($datos['Final']));
        $this->sucursal = $datos['Sucursal'];
    }

            public function sheets(): array
    {


      if ($this->hojas==1){

        $this->ventageneral = DB::connection('retail')->table('VENTASDET')->SELECT(DB::raw('VENTASDET.COD_PROD AS COD_PROD'),
            DB::raw('PRODUCTOS.DESCRIPCION'), 
            DB::raw('SUM(VENTASDET.CANTIDAD) AS CANTIDAD_S'),
            DB::raw('PRODUCTOS.LINEA'), 
            DB::raw('PRODUCTOS.MARCA'), 
             DB::raw('PRODUCTOS_AUX.PRECOSTO'),
            DB::raw('(SUM(VENTASDET.CANTIDAD)*PRODUCTOS_AUX.PRECOSTO) AS PRECOSTO_TOTAL'),
           DB::raw ('SUM(VENTASDET.PRECIO) AS PRECIO_VENTA'),
            DB::raw('VENTASDET.PRECIO_UNIT as PRECIO_UNIT_VENTA'),
            DB::raw('IFNULL(((SUM(VENTASDET.PRECIO))-(SUM(VENTASDET.CANTIDAD) *PRODUCTOS_AUX.PRECOSTO)),0) AS UTILIDAD'))
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
         ->leftjoin('PRODUCTOS_AUX',function($join){
          $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET.COD_PROD')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
         ->WHERE([ 
         ['VENTASDET.DESCRIPCION', 'not like', 'DESCUENTO%'],
         ['VENTASDET.ID_SUCURSAL','=',$sucursal],
         ['VENTASDET.ANULADO','<>',1 ]])
         ->whereBetween('VENTASDET.FECALTAS', [$inicio, $final])
         ->GROUPBY('VENTASDET.COD_PROD')
        ->GROUPBY('PRODUCTOS_AUX.PRECOSTO')
        ->GROUPBY('VENTASDET.PRECIO_UNIT')
         ->get()
         ->toArray();

           $this->descuentogeneral= DB::connection('retail')->table('VENTASDET')->SELECT(DB::raw('VENTASDET.CODIGO'),
            DB::raw('SUBSTRING(VENTASDET.DESCRIPCION,11,3) AS PORCENTAJE'),
            DB::raw('VENTASDET.CAJA'),
            DB::raw('VENTASDET.ID_SUCURSAL'),
            DB::raw('VENTASDET.ITEM')
         )
             ->where([['VENTASDET.ID_SUCURSAL','=',$sucursal],
            ['VENTASDET.DESCRIPCION', 'like', 'DESCUENTO%'],
               ['VENTASDET.cod_prod', '=','2'],
             ['VENTASDET.ANULADO','<>',1]])
               ->whereBetween('VENTASDET.FECALTAS', [$inicio, $final])
               ->get();
               foreach ($this->descuentogeneral as $this->descuento ) {
                    $this->calculo=DB::connection('retail')->table('VENTASDET')->SELECT(DB::raw('VENTASDET.COD_PROD'),
            DB::raw('VENTASDET.PRECIO'),
            DB::raw('VENTASDET.PRECIO_UNIT'),
            DB::raw('VENTASDET.ITEM')
             )
             ->where([
            ['VENTASDET.ID_SUCURSAL','=',$sucursal],
            ['VENTASDET.CODIGO', '=', $this->descuento->CODIGO],
            ['VENTASDET.DESCRIPCION', 'not like', 'DESCUENTO%'],
            ['VENTASDET.CAJA', '=',$this->descuento->CAJA],
             ])

           ->get();

                 foreach ($this->calculo as $this->calculos) {

                  if ($this->calculos->ITEM< $this->descuento->ITEM){
                    $key=array_search($this->calculos->COD_PROD, array_column($this->ventageneral, 'COD_PROD'));

                  $this->ventageneral[$key]->PRECIO_VENTA=(int)$this->ventageneral[$key]->PRECIO_VENTA-(((int)$this->calculos->PRECIO*(int)$this->descuento->PORCENTAJE)/100);

                                        # code...
                  }
                  } # code...
               }
         $this->sheets[]=new VentaMarcaPorMes(1,"a<",1,"as",$this->hojas,$this->ventageneral);
        $this->hojas=$this->hojas+1;
      } 
     


          $RESULTS= DB::connection('retail')->table('VENTASDET')->SELECT(DB::raw('PRODUCTOS.MARCA as Marca' ),
            DB::raw('MARCA.DESCRIPCION as DescriM' ),
            DB::raw('PRODUCTOS.LINEA As Linea' ),
            DB::raw('LINEAS.DESCRIPCION')
          )
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
          ->leftJoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
           ->leftJoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
         ->WHERE('VENTASDET.ID_SUCURSAL','=',$sucursal)
         ->where('VENTASDET.DESCRIPCION', 'not like', 'DESCUENTO%')
        ->whereBetween('VENTASDET.FECALTAS', [$inicio, $final])
        ->GROUPBY ('PRODUCTOS.MARCA')
        ->GROUPBY ('PRODUCTOS.LINEA')
         ->get();
         foreach ($RESULTS as $KEY  => $value) {
          if($value->DescriM==NULL){
            $descrim='Indefinido';
          }else{
            $descrim=$value->DescriM;
          }
          if($value->DESCRIPCION==NULL){
            $descrili='Indefinido';
          }else{
            $descrili=$value->DESCRIPCION;
          }
          $this->sheets[]= new VentaMarcaPorMes($value->Marca,$descrim,$value->Linea,$descrili,$this->hojas,$this->ventageneral);
           # code...
         }
       

         
        
     return $this->sheets;

      } 
}
