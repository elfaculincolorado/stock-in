<?php

use App\Http\Controllers\VentaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Models\Venta;

Route::middleware('api')->group(function () {
    Route::apiResource('productos', ProductoController::class);
    Route::get('/notificaciones', [ProductoController::class, 'notificaciones']);
});

Route::post('/ventas', [VentaController::class, 'store']);

Route::get('/ventas', function (Request $request) {
    $inicio = $request->query('inicio');
    $fin = $request->query('fin');

    $ventas = Venta::whereBetween('fecha', [$inicio, $fin])
        ->select('fecha', 'monto')
        ->orderBy('fecha')
        ->get();

    return response()->json($ventas);
});

