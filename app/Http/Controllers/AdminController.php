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

            // Valor numérico del progreso según el estado del trámite
            $progresoNumerico = match ($estadoTramite) {
                'pendiente' => 0,
                'en_proceso' => 50,
                'finalizado' => 100,
                default => 0,
            };

            // Obtener los pagos
            $pagos = $cliente->tramite?->pagos ?? collect();

            // Verificar el estado de los pagos por tipo
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
            ];
        });

        return response()->json($data);
    }



    /**
     * Funcion para el cambio del nombre de empresa.
     */
    public function CambioNombreEmpresa(CambioNombreEmpresaRequest $request ){
        $user = auth()->user();

        if ($user->rol !== 'admin') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $empresa = Empresa::find($request->empresa_id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        

        // return response()->json($posibles_nombres);
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

        $tramites = Tramite::with(['cliente.empresa', 'pagos', 'documentos'])
            ->get()
            ->map(function ($tramite) {
                $nombreCliente = $tramite->cliente
                    ? $tramite->cliente->nombre . ' ' . $tramite->cliente->apellidos
                    : 'Sin cliente';

                $nombreEmpresa = $tramite->cliente?->empresa?->nombre_empresa ?? 'Sin empresa';

                // Verificar pagos por tipo
                $pagos = $tramite->pagos ?? collect();

                $pago1 = $pagos->firstWhere('tipo_pago', 'reserva_nombre')?->estado === 'pagado';
                $pago2 = $pagos->firstWhere('tipo_pago', 'llenado_minuta')?->estado === 'pagado';

                // Obtener estado directamente del trámite
                $estadoTramite = $tramite->estado ?? 'pendiente';

                return [
                    'id' => $tramite->id,
                    'nombre_cliente' => $nombreCliente,
                    'fecha_inicio' => $tramite->fecha_inicio,
                    'fecha_fin' => $tramite->fecha_fin,
                    'estado_tramite' => $estadoTramite,
                    'nombre_empresa' => $nombreEmpresa,
                    'pago1' => $pago1,
                    'pago2' => $pago2,
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
    public function reportes()
    {
        // Parte 1: Ingresos por día
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $ingresos = [];

        foreach (range(0, 6) as $diaNum) {
            $total = DB::table('pagos')
                ->whereRaw('WEEKDAY(fecha) = ?', [$diaNum])
                ->sum('monto');
            $ingresos[] = $total;
        }

        // Parte 2: Tipos de empresa
        $resultados = DB::table('empresas')
            ->select('tipo_empresa', DB::raw('count(*) as cantidad'))
            ->groupBy('tipo_empresa')
            ->get();

        $tiposLabels = [];
        $tiposData = [];

        foreach ($resultados as $r) {
            $tiposLabels[] = $r->tipo_empresa;
            $tiposData[] = $r->cantidad;
        }

        // Respuesta combinada
        return response()->json([

            'dias' => $dias,
            'ingresos' => $ingresos,
            'labels' => $tiposLabels,
            'data' => $tiposData,
            'total_ingresos' => array_sum($ingresos), // Suma total de ingresos por día
        ]);
    }




}
