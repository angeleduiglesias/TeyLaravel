<?php

namespace App\Http\Requests\Notario;

use Illuminate\Foundation\Http\FormRequest;

class CitaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::authorize(); // Verifica autenticación
        return $this->authorizeRoles(['notario']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'cliente_id' => 'required|exists:clientes,id',
            'descripcion' => 'nullable|string|max:255',
            'titulo' => 'required|string|max:100',
        ];
    }
}
