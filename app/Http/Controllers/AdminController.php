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

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        // Nombre del admin
        $admin = Admin::where('user_id', $user->id)->first();
        $nombre_admin = $admin ? $admin->nombres+ ' ' + $admin->apellidos : 'Administrador';

        // Métricas generales
        $clientes_registrados = Cliente::count(); 
        $clientes_activos = Cliente::where('estado', 'activo')->count();
        $tramites_pendientes = Tramite::where('estado', 'pendiente')->count();

        // Últimos 15 documentos con cliente y tramite
        $documentos = Documento::with(['tramite.cliente'])
            ->latest()
            ->take(15)
            ->get();

        $tramites_recientes = $documentos->map(function ($doc) {
            $cliente = $doc->tramite->cliente ?? null;
            return [
                'tipo_documento' => $doc->tipo_documento,
                'nombre_cliente' => $cliente ? $cliente->nombre . ' ' . $cliente->apellidos : 'Sin nombre',
                'fecha_tramite' => $doc->tramite->fecha_inicio ?? null,
                'estado_tramite' => $doc->estado,
            ];
        });

        // Últimos 15 pagos con cliente
        $pagos = Pago::with(['tramite.cliente'])
            ->latest()
            ->take(15)
            ->get();

        $pagos_recientes = $pagos->map(function ($pago) {
            $cliente = $pago->tramite->cliente ?? null;
            return [
                'nombre_cliente' => $cliente ? $cliente->nombre . ' ' . $cliente->apellidos : 'Sin nombre',
                'monto_pago' => $pago->monto,
                'fecha' => $pago->fecha,
                'tipo_pago' => $pago->tipo_pago,
                'estado_pago' => $pago->estado,
            ];
        });

        // JSON de respuesta
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
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $clientes = Cliente::with([
            'empresa:id,cliente_id,tipo_empresa',
            'tramite.pagos' 
        ])->get();

        $data = $clientes->map(function ($cliente) {

            // Obtener estado del primer o último pago si existe
            $pagoEstado = optional($cliente->tramite->pagos->last())->estado ?? 'Sin pagos';

            return [
                'nombre_cliente' => $cliente->nombre . ' ' . $cliente->apellidos ?? 'Sin nombre',
                'dni' => $cliente->dni ?? 'No registrado',
                'tipo_empresa' => optional($cliente->empresa)->tipo_empresa ?? 'No registrada',
                'progreso' => optional($cliente->tramite)->estado ?? 'No iniciado', 
                'estado' => $pagoEstado == 'pagado' ? 'Pagado' : 'Pendiente',
                'contacto' => $cliente->telefono ?? 'No registrado',
            ];
        });

        return response()->json($data);
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
