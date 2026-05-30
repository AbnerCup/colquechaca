<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Transaccion;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{

    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('transacciones.index', compact('categorias'));
    }

    public function getData(Request $request)
    {
        $query = Transaccion::with('categoria');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'ilike', "%{$search}%")
                    ->orWhere('descripcion', 'ilike', "%{$search}%");
            });
        }
        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }
        $transacciones = $query->orderBy('fecha', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $runningBalance = 0;
        $totalIngresosFiltrados = 0;
        $totalEgresosFiltrados = 0;

        foreach ($transacciones as $t) {
            if ($t->tipo === 'ingreso') {
                $runningBalance += $t->monto;
                $totalIngresosFiltrados += $t->monto;
            } else {
                $runningBalance -= $t->monto;
                $totalEgresosFiltrados += $t->monto;
            }
            $t->saldo_acumulado = $runningBalance;
        }
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();
        $ingresosMes = Transaccion::where('tipo', 'ingreso')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('monto');
        $egresosMes = Transaccion::where('tipo', 'egreso')
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('monto');
        $saldoMes = $ingresosMes - $egresosMes;
        $htmlTabla = view('transacciones.partials.tabla', compact('transacciones'))->render();

        return response()->json([
            'html' => $htmlTabla,
            'totales' => [
                'ingresos' => number_format($totalIngresosFiltrados, 2, '.', ''),
                'egresos' => number_format($totalEgresosFiltrados, 2, '.', ''),
                'saldo_neto' => number_format($totalIngresosFiltrados - $totalEgresosFiltrados, 2, '.', ''),
            ],
            'resumen_mes' => [
                'ingresos' => number_format($ingresosMes, 2, '.', ''),
                'egresos' => number_format($egresosMes, 2, '.', ''),
                'saldo' => number_format($saldoMes, 2, '.', ''),
            ]
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|gt:0',
            'tipo' => 'required|in:ingreso,egreso',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $ultimaTransaccion = Transaccion::orderBy('id', 'desc')->first();
        if (!$ultimaTransaccion) {
            $numero = 1;
        } else {
            $partes = explode('-', $ultimaTransaccion->codigo);
            $ultimoNumero = isset($partes[1]) ? intval($partes[1]) : 0;
            $numero = $ultimoNumero + 1;
        }
        $codigo = 'TRX-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
        Transaccion::create([
            'codigo' => $codigo,
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
            'monto' => $request->monto,
            'tipo' => $request->tipo,
            'categoria_id' => $request->categoria_id,
        ]);

        return response()->json(['success' => 'Transacción registrada con éxito.']);
    }
    public function update(Request $request, $id)
    {
        $transaccion = Transaccion::findOrFail($id);
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|gt:0',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);
        if ($categoria->tipo !== $transaccion->tipo) {
            return response()->json([
                'errors' => [
                    'categoria_id' => ["La categoría elegida debe coincidir con el tipo ({$transaccion->tipo})."]
                ]
            ], 422);
        }
        $transaccion->update([
            'descripcion' => $request->descripcion,
            'monto' => $request->monto,
            'categoria_id' => $request->categoria_id,
        ]);

        return response()->json(['success' => 'Transacción modificada con éxito.']);
    }
    public function destroy($id)
    {
        $transaccion = Transaccion::findOrFail($id);
        $transaccion->delete();

        return response()->json(['success' => 'Transacción eliminada con éxito.']);
    }
    public function getCategoriasPorTipo($tipo)
    {
        $categorias = Categoria::where('tipo', $tipo)
            ->orderBy('nombre')
            ->get();

        return response()->json($categorias);
    }
}
