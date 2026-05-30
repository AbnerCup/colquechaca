<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaccionController;

// Vista principal
Route::get('/', [TransaccionController::class, 'index'])->name('transacciones.index');

// Rutas
Route::get('/transacciones/data', [TransaccionController::class, 'getData'])->name('transacciones.data');
Route::post('/transacciones', [TransaccionController::class, 'store'])->name('transacciones.store');
Route::put('/transacciones/{id}', [TransaccionController::class, 'update'])->name('transacciones.update');
Route::delete('/transacciones/{id}', [TransaccionController::class, 'destroy'])->name('transacciones.destroy');
Route::get('/categorias-por-tipo/{tipo}', [TransaccionController::class, 'getCategoriasPorTipo'])->name('categorias.por-tipo');
