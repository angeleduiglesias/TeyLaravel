<?php

namespace App\Http\Requests\Notario;

use Illuminate\Foundation\Http\FormRequest;

class IndexNotarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // parent::authorize(); // Verifica autenticación

        // return $this->authorizeRoles(['cliente']); // Verifica que tenga rol adecuado

        return true;
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
