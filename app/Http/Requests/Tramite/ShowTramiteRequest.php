<?php

namespace App\Http\Requests\Tramite;

use Illuminate\Foundation\Http\FormRequest;

class ShowTramiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::authorize(); // Verifica autenticación
        return $this->authorizeRoles(['cliente', 'admin']); // Verifica que tenga rol adecuado
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
