<?php

namespace App\Http\Requests\Minuta;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Base\AuthenticatedFormRequest;

class PagoMinutaRequest extends AuthenticatedFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::authorize();
        return $this->authorizeRoles(['cliente']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dni_cliente' => 'required|string',
            'estado' => 'required|in:pendiente,pagado',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tipo_pago' => 'required|in:reserva_nombre,llenado_minuta',
        ];
    }
}