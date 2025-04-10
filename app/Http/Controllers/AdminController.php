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

        $total_clientes = $clientes->count();
        $total_tramites = $tramites->count();
        $total_documentos = $documentos->count();

        // Return a JSON response with the data
        return response()->json([
            'users' => $users,
            'clientes' => $clientes,
            'tramites' => $tramites,
            'documentos' => $documentos,
            'notarios' => $notarios,
            'empresas' => $empresas,
            'total_clientes' => $total_clientes,
            'total_tramites' => $total_tramites,
            'total_documentos' => $total_documentos,
        ]);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
