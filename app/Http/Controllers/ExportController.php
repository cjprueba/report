<?php

namespace App\Http\Controllers;
use App\Exports\VentasMarca;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportController extends Controller
{

	public function __construct(){
        $this->middleware('auth');
    }

    public function mostrar(Request $request)
    {
        return Excel::download(new VentasMarca($request->all()), 'ventasMarca.xlsx');
    }
}
