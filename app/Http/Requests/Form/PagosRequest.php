<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class pagosRequest extends FormRequest
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
            'dni' => 'required|string|max:9',
            'monto' => 'required|numeric',
            'tipo_pago' => 'required|string|in:reserva_nombre,llenado_minuta',
            'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'estado' => 'required|string|in:pendiente,pagado',
        ];
    }
}
