<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambioNombreEmpresaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::authorize();
        return $this->authorizeRoles(['admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => 'required|string|id',
            'nombre_empresa' => 'nullable|string|max:255',
            'estado' => 'nullable|boolean',
        ];
    }
}
