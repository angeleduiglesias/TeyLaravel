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
        'formulario.paso_1_datos_personales.nacionalidad' => 'required|string',
        'formulario.paso_1_datos_personales.dni' => 'required|string',
        'formulario.paso_1_datos_personales.profesion' => 'required|string',
        'formulario.paso_1_datos_personales.estado_civil' => 'required|string',
        'formulario.paso_1_datos_personales.direccion' => 'required|string',
        'formulario.paso_2_datos_empresa.direccion_empresa' => 'required|string',
        'formulario.paso_2_datos_empresa.provincia_empresa' => 'required|string',
        'formulario.paso_2_datos_empresa.departamento_empresa' => 'required|string',
        'formulario.paso_2_datos_empresa.objetivo' => 'required|string',
        'formulario.paso_4_capital_y_aportes.monto_capital' => 'required|numeric|min:1',
        'formulario.paso_4_capital_y_aportes.aportes' => 'required|array|min:1',
        'formulario.paso_4_capital_y_aportes.aportes.*.descripcion' => 'required|string',
        'formulario.paso_4_capital_y_aportes.aportes.*.monto' => 'required|numeric|min:0',
        'formulario.paso_5_apoderado.apoderado' => 'nullable|string',
        'formulario.paso_5_apoderado.dni_apoderado' => 'nullable|string',
        'formulario.paso_6_confirmacion.ciudad' => 'required|string',
        'formulario.paso_6_confirmacion.fecha_registro' => 'required|date',
        'formulario.paso_3_socios' => 'nullable|array',
        'formulario.paso_3_socios.*.nombre_socio' => 'required|string',
        'formulario.paso_3_socios.*.nacionalidad_socio' => 'required|string',
        'formulario.paso_3_socios.*.dni_socio' => 'required|string',
        'formulario.paso_3_socios.*.profesion_socio' => 'required|string',
        'formulario.paso_3_socios.*.estado_civil_socio' => 'required|string',
        'formulario.paso_3_socios.*.aporte.descripcion' => 'nullable|string',
        'formulario.paso_3_socios.*.aporte.monto' => 'nullable|numeric',
    ];
}

}
