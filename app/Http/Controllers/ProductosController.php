<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Productos;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        return response()->json(Productos::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'minimo' => 'required|integer|min:0',
        ]);

        $producto = Productos::create($request->all());
        return response()->json($producto, 201);
    }

    public function show(Productos $producto)
    {
        return response()->json($producto);
    }

    public function update(Request $request, Productos $producto)
    {
        $producto->update($request->all());
        return response()->json($producto);
    }

    public function destroy(Productos $producto)
    {
        $producto->delete();
        return response()->json(null, 204);
    }

    public function notificaciones()
    {
        $productos = Productos::whereColumn('stock', '<=', 'minimo')->get();

        $notificaciones = $productos->map(function ($p) {
            return [
                'message' => "Te estás quedando sin stock de {$p->nombre} ({$p->stock} restantes)",
                'time' => now()->format('H:i:s'),
            ];
        });

        return response()->json($notificaciones);
    }
}
