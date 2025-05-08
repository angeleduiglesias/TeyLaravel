<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexAdminRequest $request)
    {
        // Validate the request
        $validated = $request->validated();

        // Fetch data from the database
        $clientes = Cliente::all();
        $tramites = Tramite::all();
        $documentos = Documento::all();
        $notarios = Notario::all();
        $empresas = Empresa::all();

        //card
        $clientes_registrados = $clientes->count();
        $clientes_activos = $clientes->where('estado', 'activo')->count();
        $tramites_pendientes = $tramites->where('estado', 'pendiente')->count();

        // mostrar los tramites recientes pero con solo 15 registros
        $tramites_recientes = $tramites->where('created_at', '>=', now()->subDays(30))->take(15)->get();

        //mostrar los pagos recientes pero con solo 10 registros
        $pagos_recientes = Pago::where('created_at', '>=', now()->subDays(30))->take(10)->get();

        // Return a JSON response with the data
        return response()->json([
            $clientes,
            $tramites,
            $documentos,
            $notarios,
            $empresas,
            $clientes_registrados,
            $clientes_activos,
            $tramites_pendientes,
            $tramites_recientes,
            $pagos_recientes,
        ]);
    }
    

    /**
     * Funcion para enviar todos los datos del Cliente.
     */
    public function clientes()
    {
        $clientes = Cliente::all();
        return response()->json($clientes);
    }

    /**
     * Funcion para enviar todos los datos del Notario.
     */
    public function notario()
    {
        $notarios = Notario::all();
        return response()->json($notarios);
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
