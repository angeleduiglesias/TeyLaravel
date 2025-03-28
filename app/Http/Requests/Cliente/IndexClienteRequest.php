<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class IndexClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->check() && auth()->user()->rol === 'admin') {
            return true;
        }

        // Lanzamos una excepción con una respuesta personalizada si el usuario no está autorizado.
        throw new HttpResponseException(response()->json([
            'error' => 'No tienes permiso para acceder a esta información.'
        ], 403));
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
