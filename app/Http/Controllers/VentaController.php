<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;

class VentaController extends Controller
{
    //registra una nueva venta
   public function store(Request $request)
{
    $data = $request->validate([
        'producto_id' => 'required|exists:productos,id',
        'cantidad' => 'required|integer|min:1',
        'fecha' => 'required|date',
    ]);

    $producto = Producto::find($data['producto_id']);

    if ($producto->stock < $data['cantidad']) {
        return response()->json(['error' => 'No hay suficiente stock'], 400);
    }

    $venta = Venta::create($data);

    $producto->decrement('stock', $data['cantidad']);

    return response()->json(['message' => 'Venta registrada correctamente', 'venta' => $venta]);
}


    //muestra ventas filtradas por fecha (sirve para el grafico de ganancias)
    public function index(Request $request)
    {
        $inicio = $request->query('inicio');
        $fin = $request->query('fin');

        //valida fechas
        if (!$inicio || !$fin) {
            return response()->json(['error' => 'Debe ingresar una fecha de inicio y fin'], 400);
        }

        $ventas = Venta::whereBetween('fecha', [$inicio, $fin])
            ->select('fecha', 'cantidad', 'producto_id')
            ->with('producto:id,nombre,precio')
            ->orderBy('fecha', 'asc')
            ->get();
            return response()->json($ventas);

        //calcula las ganancias por venta
        $ventasFormateadas = $ventas->map(function ($venta) {
            $ganancia = $venta->cantidad * $venta->producto->precio;
            return [
                'fecha' => $venta->fecha,
                'producto' => $venta->producto->nombre,
                'cantidad' => $venta->cantidad,
                'ganancia' => $ganancia
            ];
        });

        return response()->json($ventasFormateadas);
    }
}


