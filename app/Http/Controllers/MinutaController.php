<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Minuta;
use App\Models\Socio;
use App\Models\Aporte;
use App\Http\Requests\Minuta\StoreMinutaRequest;
use App\Http\Requests\Minuta\PagoMinutaRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Documento;
use App\Models\Cliente;
use App\Models\Tramite;
use App\Models\Pago;
use App\Models\Notario;

class MinutaController extends Controller
{
    public function store(StoreMinutaRequest $request)
    {
        $user = auth()->user();

        if ($user->rol !== 'cliente') {
            return response()->json(['message' => 'No tienes permisos o no te encuentras registrado.'], 403);
        }

        $cliente = $user->cliente;

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado.'], 404);
        }

        $tramite = $cliente->tramite;
        $empresa = $cliente->empresa;

        $estadoTramite = $tramite?->estado;
        $progresoNumerico = match ($estadoTramite) {
            'pendiente' => 0,
            'en_proceso' => 50,
            'finalizado' => 100,
            default => 0,
        };

        $fechaInicio = $tramite?->fecha_inicio;

        $pagos = $tramite?->pagos ?? collect();
        $reservaNombre = $pagos->firstWhere('tipo_pago', 'reserva_nombre');
        $minuta = $pagos->firstWhere('tipo_pago', 'llenado_minuta');

        $datosPersonales = [
            'nacionalidad' => $cliente->nacionalidad ?? '',
            'profesion' => $cliente->profesion ?? '',
            'estado_civil' => $cliente->estado_civil ?? '',
            'direccion' => $cliente->direccion ?? '',
            'nombre_conyuge' => $cliente->nombre_conyuge ?? '',
            'dni_conyuge' => $cliente->dni_conyuge ?? '',
        ];

        $datosEmpresa = [
            'nombre_empresa' => $empresa?->nombre_empresa ?? '',
            'direccion_empresa' => $empresa?->direccion_empresa ?? '',
            'provincia_empresa' => $empresa?->provincia_empresa ?? '',
            'departamento_empresa' => $empresa?->departamento_empresa ?? '',
            'objetivo' => $empresa?->objetivo ?? '',
        ];

        // ⚠ CORRECCIÓN AQUÍ
        $socios = collect($empresa?->socios ?? [])->map(function ($socio) {
            return [
                'nombre_socio' => $socio->nombre_socio,
                'nacionalidad_socio' => $socio->nacionalidad_socio,
                'dni_socio' => $socio->dni_socio,
                'profesion_socio' => $socio->profesion_socio,
                'estado_civil_socio' => $socio->estado_civil_socio,
                'nombre_conyuge_socio' => $socio->nombre_conyuge_socio,
                'dni_conyuge_socio' => $socio->dni_conyuge_socio,
                'aportes' => collect($socio->aportes ?? [])->map(function ($aporte) {
                    return [
                        'descripcion' => $aporte->descripcion,
                        'monto' => $aporte->monto,
                    ];
                })->toArray(),
            ];
        })->toArray();

        $capitalAportes = [
            'monto_capital' => $empresa?->monto_capital ?? 0,
            'aportes' => collect($empresa?->aportes ?? [])->map(function ($aporte) {
                return [
                    'descripcion' => $aporte->descripcion,
                    'monto' => $aporte->monto,
                ];
            })->toArray(),
        ];

        $datosApoderado = [
            'apoderado' => $empresa?->apoderado ?? '',
            'dni_apoderado' => $empresa?->dni_apoderado ?? '',
        ];

        $confirmacion = [
            'ciudad' => $tramite?->ciudad ?? '',
            'fecha_registro' => $tramite?->fecha_registro ?? '',
        ];

        return response()->json([
            'nombre_cliente' => $cliente->nombre . ' ' . $cliente->apellidos,
            'estado_tramite' => [
                'fecha_inicio' => $fechaInicio,
                'estado' => $estadoTramite ?? 'sin trámite',
                'progreso' => $progresoNumerico
            ],
            'estado_pagos' => [
                'pago1' => isset($reservaNombre) && $reservaNombre->estado === 'pagado',
                'pago2' => isset($minuta) && $minuta->estado === 'pagado',
            ],
            'formulario' => [
                'paso_1' => $datosPersonales,
                'paso_2' => $datosEmpresa,
                'paso_3' => $socios,
                'paso_4' => $capitalAportes,
                'paso_5' => $datosApoderado,
                'paso_6' => $confirmacion,
            ],
        ]);
    }



    public function pagos(PagoMinutaRequest $request)
    {
        $user = auth()->user();

        if ($user->rol !== 'cliente') {
            return response()->json(['message' => 'No tienes permisos o no te encuentras registrado.'], 403);
        }

        $data = $request->validated();
        $cliente = $user->cliente;

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado.'], 404);
        }

        if ($data['dni_cliente'] !== $cliente->dni) {
            return response()->json(['message' => 'DNI del cliente no coincide con el registrado.'], 400);
        }

        $tramite = $cliente->tramite;

        if (!$tramite) {
            return response()->json(['message' => 'Trámite no encontrado para este cliente.'], 404);
        }

        DB::beginTransaction();

        try {
            // Evitar duplicados
            $pagoExistente = $tramite->pagos()->where('tipo_pago', 'llenado_minuta')->first();
            if ($pagoExistente) {
                return response()->json(['message' => 'Este trámite ya tiene un pago registrado para la minuta.'], 409);
            }

            // Registrar el pago
            $pago = Pago::create([
                'estado' => $data['estado'],
                'monto' => $data['monto'],
                'fecha' => now(),
                'comprobante' => $data['comprobante'] ?? null,
                'tipo_pago' => $data['tipo_pago'],
                'tramite_id' => $tramite->id,
            ]);

            // Selección del notario (primer notario disponible)
            $notario = Notario::first();
            if (!$notario) {
                DB::rollBack();
                return response()->json(['message' => 'No hay notarios disponibles.'], 500);
            }

            // Crear el documento de minuta
            Documento::create([
                'estado' => 'pendiente',
                'observaciones' => 'Sin Observaciones',
                'tipo_documento' => 'minuta',
                'tramite_id' => $tramite->id,
                'notario_id' => $notario->id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Pago registrado y documento de minuta creado con éxito.'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar el pago y la creación del documento.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }





}