<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'estado' => 'required|in:pendiente,pagado',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tipo_pago' => 'required|in:reserva_nombre,llenado_minuta',
        ];
    }
}
