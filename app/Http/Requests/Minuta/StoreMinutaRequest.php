<?php

namespace App\Http\Requests\Minuta;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Base\AuthenticatedFormRequest;


class StoreMinutaRequest extends AuthenticatedFormRequest
{
    public function authorize(): bool
    {
        parent::authorize();
        return $this->authorizeRoles(['cliente']);
    }

    public function rules(): array
    {
        return [
            // Paso 1: DatosPersonales
            'formulario.paso_1.nacionalidad' => 'required|string',
            'formulario.paso_1.profesion' => 'required|string',
            'formulario.paso_1.estado_civil' => 'required|string',
            'formulario.paso_1.direccion' => 'required|string',
            'formulario.paso_1.nombre_conyuge' => 'nullable|string',
            'formulario.paso_1.dni_conyuge' => 'nullable|string',

            // Paso 2: DatosEmpresa
            'formulario.paso_2.nombre_empresa' => 'required|string',
            'formulario.paso_2.direccion_empresa' => 'nullable|string',
            'formulario.paso_2.provincia_empresa' => 'required|string',
            'formulario.paso_2.departamento_empresa' => 'required|string',
            'formulario.paso_2.objetivo' => 'required|string',

            // Paso 3: Socios
            'formulario.paso_3' => 'nullable|array',
            'formulario.paso_3.*.nombre_socio' => 'required|string',
            'formulario.paso_3.*.nacionalidad_socio' => 'required|string',
            'formulario.paso_3.*.dni_socio' => 'required|string',
            'formulario.paso_3.*.profesion_socio' => 'required|string',
            'formulario.paso_3.*.estado_civil_socio' => 'required|string',
            'formulario.paso_3.*.nombre_conyuge_socio' => 'nullable|string',
            'formulario.paso_3.*.dni_conyuge_socio' => 'nullable|string',
            'formulario.paso_3.*.aportes' => 'required|array|min:1',
            'formulario.paso_3..aportes..descripcion' => 'required|string',
            'formulario.paso_3..aportes..monto' => 'required|numeric|min:0',

            // Paso 4: CapitalAportes
            'formulario.paso_4.monto_capital' => 'required|numeric|min:1',
            'formulario.paso_4.aportes' => 'required|array|min:1',
            'formulario.paso_4.aportes.*.descripcion' => 'required|string',
            'formulario.paso_4.aportes.*.monto' => 'required|numeric|min:0',

            // Paso 5: DatosApoderado
            'formulario.paso_5.apoderado' => 'nullable|string',
            'formulario.paso_5.dni_apoderado' => 'nullable|string',

            // Paso 6: Confirmacion
            'formulario.paso_6.ciudad' => 'required|string',
            'formulario.paso_6.fecha_registro' => 'required|date',
        ];
    }

}