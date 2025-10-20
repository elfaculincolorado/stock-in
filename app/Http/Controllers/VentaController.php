<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;

class VentaController extends Controller
{
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

        //crear la venta
        Venta::create($data);

        //descontar del stock
        $producto->decrement('stock', $data['cantidad']);

        return response()->json(['message' => 'Venta registrada correctamente']);
    }
}

