<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Tramite;
use App\Models\Documento;
use App\Models\Notario;
use App\Models\Empresa;
use App\Models\Pago;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Verifica si el usuario es admin
        if ($request->user()->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        // envia el nombre del admin
        $admin = Admin::where('user_id', $user->id)->first();
        $nombre_admin = $admin ? $admin->nombres : null;

        // proceso para obtener el tipo de documento por el id del cliente
        $cliente = Cliente::first(); 
        $tramite = $cliente ? Tramite::where('cliente_id', $cliente->id)->first() : null;
        $documento = $tramite ? Documento::where('tramite_id', $tramite->id)->first() : null;

        $tipo_documento = $documento ? $documento->tipo_documento : 'Desconocido';
        $nombre_cliente = $cliente ? $cliente->nombre . ' ' . $cliente->apellidos : 'Sin nombre';
        $fecha_tramite = $tramite ? $tramite->fecha_inicio : null;
        $estado_tramite = $tramite ? $tramite->estado : null;

        $clientes_registrados = Cliente::count(); 
        $clientes_activos = Cliente::where('estado', 'activo')->count();
        $tramites_pendientes = Tramite::where('estado', 'pendiente')->count();

        $tramites_recientes = [
            [
                'tipo_documento' => $tipo_documento,
                'cliente' => $nombre_cliente,
                'fecha_tramite' => $fecha_tramite,
                'estado_tramite' => $estado_tramite
            ]
        ];

        $pago = Pago::latest()->first(); // último pago

        $pagos_recientes = [
            [
                'cliente' => $nombre_cliente,
                'monto_pago' => $pago ? $pago->monto : null,
                'fecha' => $pago ? $pago->fecha : null,
                'tipo_pago' => $pago ? $pago->tipo_pago : null
            ]
            
        ];

        // return logger($tramites_recientes);

        // Return a JSON response with the data
        return response()->json([
            'nombre_admin' => $nombre_admin,
            'clientes_registrados' => $clientes_registrados,
            'clientes_activos' => $clientes_activos,
            'tramites_pendientes' => $tramites_pendientes,
            'tramites_recientes' => $tramites_recientes,
            'pagos_recientes' => $pagos_recientes,
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
