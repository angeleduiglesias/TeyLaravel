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
use App\Http\Requests\Admin\StoreAdminRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;



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
            ->take(10)
            ->get();

        $tramites_recientes = $documentos->map(function ($doc) {
            $cliente = $doc->tramite->cliente ?? null;
            return [
                'cliente_id' => $cliente?->id ?? null, 
                'tipo_documento' => $doc->tipo_documento,
                'nombre_cliente' => $cliente ? $cliente->nombre . ' ' . $cliente->apellidos : 'Sin nombre',
                'fecha_tramite' => $doc->tramite->fecha_inicio ?? null,
                'estado_tramite' => $doc->estado,
            ];
        });

        // Últimos 10 pagos con cliente
        $pagos = Pago::with(['tramite.cliente'])
            ->latest()
            ->take(10)
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
        $clientes = Cliente::with('empresa.posiblesNombres')->get();

        $reserva_nombre = [];

        foreach ($clientes as $cliente) {
            $empresa = $cliente->empresa;
            $posibles = $empresa?->posiblesNombres;

            $reserva_nombre[] = [
                'cliente_id' => $cliente->id,
                'nombre_cliente' => $cliente->nombre . ' ' . $cliente->apellidos,
                'nombre_empresa' => $empresa?->nombre_empresa ?? 'Sin empresa',
                'tipo_empresa' => $empresa?->tipo_empresa ?? 'Sin tipo',
                'posible_nombre1' => $posibles?->posible_nombre1 ?? 'Sin nombre',
                'posible_nombre2' => $posibles?->posible_nombre2 ?? 'Sin nombre',
                'posible_nombre3' => $posibles?->posible_nombre3 ?? 'Sin nombre',
                'posible_nombre4' => $posibles?->posible_nombre4 ?? 'Sin nombre',
            ];
        }


        // JSON de respuesta
        return response()->json([
            'nombre_admin' => $nombre_admin,
            'clientes_registrados' => $clientes_registrados,
            'clientes_activos' => $clientes_activos,
            'tramites_pendientes' => $tramites_pendientes,
            'tramites_recientes' => $tramites_recientes,
            'pagos_recientes' => $pagos_recientes,
            'reserva_nombre' => $reserva_nombre
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
            'empresa:id,cliente_id,tipo_empresa,nombre_empresa',
            'tramite.pagos',
            'user:id,email'
        ])->get();

        $data = $clientes->map(function ($cliente) {
            $estadoTramite = optional($cliente->tramite)->estado;

            $progresoNumerico = match ($estadoTramite) {
                'pendiente' => 0,
                'en_proceso' => 50,
                'finalizado' => 100,
                default => 0,
            };

            $pagos = $cliente->tramite?->pagos ?? collect();

            $pago1 = $pagos->firstWhere('tipo_pago', 'reserva_nombre')?->estado === 'pagado';
            $pago2 = $pagos->firstWhere('tipo_pago', 'llenado_minuta')?->estado === 'pagado';

            return [
                'id' => $cliente->id,
                'nombre_cliente' => ($cliente->nombre ?? '') . ' ' . ($cliente->apellidos ?? ''),
                'dni' => $cliente->dni ?? 'No registrado',
                'tipo_empresa' => optional($cliente->empresa)->tipo_empresa ?? 'No registrada',
                'nombre_empresa' => optional($cliente->empresa)->nombre_empresa ?? 'No registrada',
                'progreso' => $progresoNumerico,
                'pago1' => $pago1, 
                'pago2' => $pago2, 
                'telefono' => $cliente->telefono ?? 'No registrado',
                'email' => $cliente->user->email ?? 'No registrado',
                'fecha_registro' => $cliente->created_at->format('Y-m-d'),
                'estado' => $cliente->estado ?? 'No especificado', // ← Aquí se añade el estado
            ];
        });

        return response()->json($data);
    }



    /**
     * Funcion para el cambio del nombre de empresa.
     */

    public function CambioNombreEmpresa(CambioNombreEmpresaRequest $request)
    {
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $cliente = Cliente::find($request->cliente_id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $empresa = $cliente->empresa;
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada para este cliente'], 404);
        }

        $tramite = $cliente->tramite;
        if (!$tramite) {
            return response()->json(['message' => 'Trámite no encontrado para este cliente'], 404);
        }

        // Buscar el documento pendiente de tipo 'reserva_nombre' asociado a ese tramite
        $documento = Documento::where('tramite_id', $tramite->id)
            ->where('estado', 'pendiente')
            ->where('tipo_documento', 'reserva_nombre')
            ->first();

        if (!$documento) {
            return response()->json(['message' => 'Documento pendiente de reserva de nombre no encontrado'], 404);
        }

        // Actualizar el nombre de la empresa
        $empresa->nombre_empresa = $request->nombre_empresa;
        $empresa->save();

        // Actualizar el estado del documento a aprobado
        $documento->estado = 'aprobado';
        $documento->save();

        return response()->json([
            'message' => 'Nombre de empresa actualizado y documento aprobado exitosamente'
        ]);
    }



    /**
     * Funcion para enviar todos los datos del Notario.
     */
    public function notarios()
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
    public function tramites()
    {
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $tramites = Tramite::with(['cliente.empresa', 'documentos'])
            ->get()
            ->map(function ($tramite) {
                $nombreCliente = $tramite->cliente
                    ? $tramite->cliente->nombre . ' ' . $tramite->cliente->apellidos
                    : 'Sin cliente';

                $nombreEmpresa = $tramite->cliente_id?->empresa?->nombre_empresa ?? 'Sin empresa';

                // Aquí puedes incluir los documentos en bruto o formatearlos como quieras:
                $documentos = $tramite->documentos->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'nombre_documento' => $doc->nombre_documento, // ajusta al nombre real
                        'url' => $doc->url, // o cualquier otro dato relevante
                    ];
                });

                return [
                    'id' => $tramite->id,
                    'nombre_cliente' => $nombreCliente,
                    'nombre_empresa' => $nombreEmpresa,
                    'fecha_inicio' => $tramite->fecha_inicio,
                    'fecha_fin' => $tramite->fecha_fin,
                    'estado_tramite' => $tramite->estado ?? 'pendiente',
                    'documentos' => $documentos,
                ];
            });

        return response()->json($tramites);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function pagos()
    {
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $pagos = Pago::with(['tramite.cliente'])
            ->latest()
            ->get();

        $resultado = $pagos->map(function ($pago) {
            $cliente = $pago->tramite->cliente ?? null;

            return [
                'id' => $cliente?->id ?? null,
                'nombre_cliente' => $cliente ? $cliente->nombre . ' ' . $cliente->apellidos : 'Sin nombre',
                'dni' => $cliente?->dni ?? 'Sin DNI',
                'tipo_pago' => $pago->tipo_pago,
                'monto' => $pago->monto,
                'fecha' => $pago->fecha,
                'estado_pago' => $pago->estado,
                'forma_pago' => $pago->forma_pago ?? 'Sin forma',
            ];
        });

        return response()->json($resultado);
    }


    /**
     * Funcion para el envio de datos sobre los reportes para el panel de administrador.
     */
    public function ingresosPorDiaEloquent(StoreAdminRequest $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfWeek());
            $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfWeek());

            // Usando modelo Eloquent
            $pagos = \App\Models\Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->where('estado', 'pagado') // Solo pagos completados
                // ->where('tipo_pago', 'reserva_nombre') // Si quieres filtrar por tipo específico
                ->get()
                ->groupBy(function($pago) {
                    return Carbon::parse($pago->fecha)->locale('es')->dayName;
                })
                ->map(function($group) {
                    return $group->sum('monto');
                });

            $diasOrden = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            $ingresosPorDia = [];
            
            foreach ($diasOrden as $dia) {
                $ingresosPorDia[$dia] = $pagos->get($dia, 0);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => array_keys($ingresosPorDia),
                    'ingresos' => array_values($ingresosPorDia),
                    'total_semanal' => array_sum($ingresosPorDia)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los ingresos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function configuracion()
    {
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $data = $request->validated();

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make('DitechPeru2025'),
            'rol' => 'admin',
            'remember_token' => Str::random(60),
        ]);

        $admin = Admin::create([
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'telefono' => $data['telefono'],
            'user_id' => $user->id,
            'remember_token' => Str::random(60),
        ]);

        return response()->json([
            'message' => 'Administrador creado exitosamente',
        ], 201);
    }


}
