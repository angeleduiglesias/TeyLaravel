<?php

namespace App\Http\Requests\Notario;

use Illuminate\Foundation\Http\FormRequest;

class DestroyNotarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::authorize(); // Verifica autenticación
        return $this->authorizeRoles(['admin']); // Verifica que tenga rol adecuado
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
        ];
    }
}
