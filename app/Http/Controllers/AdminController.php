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
use App\Models\PosiblesNombres;
use App\Http\Requests\Admin\CambioNombreEmpresaRequest;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        // Nombre del admin
        $admin = Admin::where('user_id', $user->id)->first();
        $nombre_admin = $admin ? $admin->nombres . ' ' . $admin->apellidos : 'Administrador';

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

        //Nombre del cliente con el nombre de la empresa
        $clientes = Cliente::with('empresa')->get();
        $nombre_empresa = $clientes->map(function ($cliente) {
            return [
                'nombre_cliente' => $cliente->nombre . ' ' . $cliente->apellidos,
                'nombre_empresa' => $cliente->empresa ? $cliente->empresa->nombre : 'Sin empresa',
                'tipo_empresa' => $cliente->empresa ? $cliente->empresa->tipo_empresa : 'Sin tipo de empresa',
            ];
        });

        //Posibles nombres de empresa
        $nombres_posibles = PosiblesNombres::with('empresa')->get();
        $posibles_nombres = $nombres_posibles->map(function ($item) {
            $empresa = $item->empresa;
            return [
                'posible_nombre1' => $empresa?->posible_nombre1 ?? 'Sin nombre',
                'posible_nombre2' => $empresa?->posible_nombre2 ?? 'Sin nombre',
                'posible_nombre3' => $empresa?->posible_nombre3 ?? 'Sin nombre',
                'posible_nombre4' => $empresa?->posible_nombre4 ?? 'Sin nombre',
            ];
        });

        $reserva_nombre = [
            'posibles_nombres' => $posibles_nombres,
            'nombre_empresa' => $nombre_empresa,
        ];

        // JSON de respuesta
        return response()->json([
            'nombre_admin' => $nombre_admin,
            'clientes_registrados' => $clientes_registrados,
            'clientes_activos' => $clientes_activos,
            'tramites_pendientes' => $tramites_pendientes,
            'tramites_recientes' => $tramites_recientes,
            'pagos_recientes' => $pagos_recientes,
            'reserva_nombre' => $reserva_nombre,
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
            'tramite.pagos',
            'user:id,email'
        ])->get();

        $data = $clientes->map(function ($cliente) {
        $pagoEstado = $cliente->tramite?->pagos?->last()?->estado ?? 'Sin pagos';
            return [
                'nombre_cliente' => ($cliente->nombre ?? '') . ' ' . ($cliente->apellidos ?? ''),
                'dni' => $cliente->dni ?? 'No registrado',
                'tipo_empresa' => optional($cliente->empresa)->tipo_empresa ?? 'No registrada',
                'progreso' => optional($cliente->tramite)->estado ?? 'No iniciado', 
                'estado' => $pagoEstado == 'pagado' ? 'Pagado' : 'Pendiente',
                'contacto' => $cliente->telefono ?? 'No registrado',
                'email' => $cliente->user->email ?? 'No registrado',
            ];
        });

        return response()->json($data);
    }


    /**
     * Funcion para el cambio del nombre de empresa.
     */
    public function cambioNombreEmpresa(CambioNombreEmpresaRequest $request ){
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        

        return response()->json($posibles_nombres);
    }


    /**
     * Funcion para enviar todos los datos del Notario.
     */
    public function notario()
    {
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }
        
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
