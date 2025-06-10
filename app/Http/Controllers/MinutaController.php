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
            $form = $request->input('formulario');
            $tipoFormulario = $request->input('tipo_formulario');

            // Crear la Minuta
            $minuta = Minuta::create([
                'nacionalidad' => $form['paso_1_datos_personales']['nacionalidad'],
                'dni' => $form['paso_1_datos_personales']['dni'],
                'profesion' => $form['paso_1_datos_personales']['profesion'],
                'estado_civil' => $form['paso_1_datos_personales']['estado_civil'],
                'direccion' => $form['paso_1_datos_personales']['direccion'],
                'nombre_conyuge' => $form['paso_1_datos_personales']['nombre_conyuge'] ?? null,
                'dni_conyuge' => $form['paso_1_datos_personales']['dni_conyuge'] ?? null,
                'direccion_empresa' => $form['paso_2_datos_empresa']['direccion_empresa'],
                'provincia_empresa' => $form['paso_2_datos_empresa']['provincia_empresa'],
                'departamento_empresa' => $form['paso_2_datos_empresa']['departamento_empresa'],
                'objetivo' => $form['paso_2_datos_empresa']['objetivo'],
                'monto_capital' => $form['paso_3_capital_y_aportes']['monto_capital'],
                'apoderado' => $form['paso_4_apoderado']['apoderado'] ?? null,
                'dni_apoderado' => $form['paso_4_apoderado']['dni_apoderado'] ?? null,
                'ciudad' => $form['paso_5_confirmacion']['ciudad'],
                'fecha_registro' => $form['paso_5_confirmacion']['fecha_registro'],
                'tipo_formulario' => $tipoFormulario,
            ]);

            // Si es EIRL, procesar solo aportes generales
            if (in_array($tipoFormulario, ['eirl_bienes_dinerarios', 'eirl_bienes_no_dinerarios'])) {
                if (!empty($form['paso_3_capital_y_aportes']['aportes'])) {
                    foreach ($form['paso_3_capital_y_aportes']['aportes'] as $aporteData) {
                        Aporte::create([
                            'descripcion' => $aporteData['descripcion'],
                            'monto' => $aporteData['monto'],
                            'minuta_id' => $minuta->id,
                        ]);
                    }
                }
            } else {
                // Si no es EIRL, procesar socios y sus aportes
                if (isset($form['paso_6_socios']) && is_array($form['paso_6_socios'])) {
                    foreach ($form['paso_6_socios'] as $socioData) {
                        $socio = Socio::create([
                            'nombre' => $socioData['nombre'],
                            'nacionalidad' => $socioData['nacionalidad'],
                            'dni' => $socioData['dni'],
                            'profesion' => $socioData['profesion'],
                            'estado_civil' => $socioData['estado_civil'],
                            'nombre_conyuge' => $socioData['nombre_conyuge'] ?? null,
                            'dni_conyuge' => $socioData['dni_conyuge'] ?? null,
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
