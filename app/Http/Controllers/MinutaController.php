<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Minuta;
use App\Models\Socio;
use App\Models\Aporte;
use App\Http\Requests\Minuta\StoreMinutaRequest;
use Illuminate\Support\Facades\DB;

class MinutaController extends Controller
{
    public function store(StoreMinutaRequest $request)
{
    DB::beginTransaction();

    try {
        $user = auth()->user();

        if ($user->rol !== 'cliente') {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        // Obtenemos los datos validados y accedemos a la clave 'formulario'
        $formulario = $request->validated()['formulario'];

        $cliente = $user->cliente;
        $empresa = $cliente->empresa ?? null;

        if (!$cliente || !$empresa) {
            return response()->json(['message' => 'Datos del cliente o empresa no encontrados.'], 404);
        }

        $minuta = Minuta::create([
            'nombre' => $cliente->nombre,
            'dni' => $cliente->dni,
            'nacionalidad' => $formulario['paso_1_datos_personales']['nacionalidad'],
            'profesion' => $formulario['paso_1_datos_personales']['profesion'],
            'estado_civil' => $formulario['paso_1_datos_personales']['estado_civil'],
            'direccion' => $formulario['paso_1_datos_personales']['direccion'],
            'nombre_conyuge' => $formulario['paso_1_datos_personales']['nombre_conyuge'] ?? null,
            'dni_conyuge' => $formulario['paso_1_datos_personales']['dni_conyuge'] ?? null,
            'nombre_empresa' => $empresa->nombre_empresa,
            'tipo_aporte' => $empresa->tipo_aporte,
            'tipo_empresa' => $empresa->tipo_empresa,
            'direccion_empresa' => $formulario['paso_2_datos_empresa']['direccion_empresa'],
            'provincia_empresa' => $formulario['paso_2_datos_empresa']['provincia_empresa'],
            'departamento_empresa' => $formulario['paso_2_datos_empresa']['departamento_empresa'],
            'objetivo' => $formulario['paso_2_datos_empresa']['objetivo'],
            'monto_capital' => $formulario['paso_4_capital_y_aportes']['monto_capital'],
            'apoderado' => $formulario['paso_5_apoderado']['apoderado'] ?? null,
            'dni_apoderado' => $formulario['paso_5_apoderado']['dni_apoderado'] ?? null,
            'ciudad' => $formulario['paso_6_confirmacion']['ciudad'],
            'fecha_registro' => $formulario['paso_6_confirmacion']['fecha_registro'],
        ]);

        // Procesar aportes generales
        foreach ($formulario['paso_4_capital_y_aportes']['aportes'] as $aporteData) {
            Aporte::create([
                'descripcion' => $aporteData['descripcion'],
                'monto' => $aporteData['monto'],
                'minuta_id' => $minuta->id,
            ]);
        }

        // Procesar socios (si existen)
        if (!empty($formulario['paso_3_socios']) && is_array($formulario['paso_3_socios'])) {
            foreach ($formulario['paso_3_socios'] as $socioData) {
                $socio = Socio::create([
                    'nombre' => $socioData['nombre_socio'],
                    'nacionalidad' => $socioData['nacionalidad_socio'],
                    'dni' => $socioData['dni_socio'],
                    'profesion' => $socioData['profesion_socio'],
                    'estado_civil' => $socioData['estado_civil_socio'],
                    'nombre_conyuge' => $socioData['nombre_conyuge_socio'] ?? null,
                    'dni_conyuge' => $socioData['dni_conyuge_socio'] ?? null,
                    'minuta_id' => $minuta->id,
                ]);

                if (isset($socioData['aporte'])) {
                    Aporte::create([
                        'descripcion' => $socioData['aporte']['descripcion'] ?? '',
                        'monto' => $socioData['aporte']['monto'] ?? 0,
                        'minuta_id' => $minuta->id,
                        'socio_id' => $socio->id,
                    ]);
                }
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Minuta registrada exitosamente.',
            'minuta_id' => $minuta->id
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Error al registrar la minuta.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
