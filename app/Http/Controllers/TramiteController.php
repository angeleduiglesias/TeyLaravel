<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tramite\StoreTramiteRequest;
use Illuminate\Http\Request;
use App\Models\Tramite;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class TramiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTramiteRequest $request)
    {
        $data = $request->validated();
         // Obtener el id del cliente del usuario autenticado
        $cliente = Auth::user()->cliente;
        if (!$cliente) {
            return response()->json(['error' => 'No se encontró el cliente asociado al usuario autenticado.'], 422);
        }
        $clienteId = $cliente->id;
        
        $tramite = Tramite::create([
            'estado' => $data['estado'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'cliente_id' => $clienteId,
        ]);

        return response()->json([
            'message' => 'Trámite creado exitosamente',
            'tramite' => $tramite,
        ], 201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowTramiteRequest $request, string $id)
    {
        // Mostramos el tramite asociado al clientes por su id
        $tramite = Tramite::where('id', $id)
            ->where('cliente_id', Auth::user()->cliente->id)
            ->first();
        if (!$tramite) {
            return response()->json(['error' => 'Trámite no encontrado'], 404);
        }
        return response()->json($tramite, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
