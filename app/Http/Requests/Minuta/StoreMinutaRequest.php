<?php

namespace App\Http\Requests\Minuta;

use Illuminate\Foundation\Http\FormRequest;

class StoreMinutaRequest extends FormRequest
{
    public function authorize(): bool
    {
        parent::authorize();
        return $this->authorizeRoles(['cliente']);
    }

    public function rules(): array
    {
        return [
            // Paso 1: Datos personales
            'formulario.paso_1_datos_personales.nombre' => 'required|string|max:255',
            'formulario.paso_1_datos_personales.nacionalidad' => 'required|string|max:255',
            'formulario.paso_1_datos_personales.dni' => 'required|string|max:20',
            'formulario.paso_1_datos_personales.profesion' => 'required|string|max:255',
            'formulario.paso_1_datos_personales.estado_civil' => 'required|string|max:50',
            'formulario.paso_1_datos_personales.direccion' => 'required|string|max:255',
            'formulario.paso_1_datos_personales.nombre_conyuge' => 'nullable|string|max:255',
            'formulario.paso_1_datos_personales.dni_conyuge' => 'nullable|string|max:20',

            // Paso 2: Datos empresa
            'formulario.paso_2_datos_empresa.nombre_empresa' => 'required|string|max:255',
            'formulario.paso_2_datos_empresa.direccion_empresa' => 'required|string|max:255',
            'formulario.paso_2_datos_empresa.provincia_empresa' => 'required|string|max:100',
            'formulario.paso_2_datos_empresa.departamento_empresa' => 'required|string|max:100',
            'formulario.paso_2_datos_empresa.objetivo' => 'required|string|max:500',

            // Paso 3: Capital y aportes
            'formulario.paso_3_capital_y_aportes.monto_capital' => 'required|numeric|min:0',
            'formulario.paso_3_capital_y_aportes.aportes' => 'required|array|min:1',
            'formulario.paso_3_capital_y_aportes.aportes.*.descripcion' => 'required|string|max:255',
            'formulario.paso_3_capital_y_aportes.aportes.*.monto' => 'required|numeric|min:0',

            // Paso 4: Apoderado
            'formulario.paso_4_apoderado.apoderado' => 'nullable|string|max:255',
            'formulario.paso_4_apoderado.dni_apoderado' => 'nullable|string|max:20',

            // Paso 5: Confirmación
            'formulario.paso_5_confirmacion.ciudad' => 'required|string|max:100',
            'formulario.paso_5_confirmacion.fecha_registro' => 'required|date',

            // Globales
            'tipo_formulario' => 'required|in:eirl_bienes_dinerarios,eirl_bienes_no_dinerarios',
            'nombre_empresa' => 'required|string|max:255',
        ];
    }
}
