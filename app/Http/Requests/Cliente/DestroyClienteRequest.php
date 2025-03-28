<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class DestroyClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Solo los usuarios con rol de 'admin' pueden eliminar clientes
        if (auth()->check() && auth()->user()->rol === 'admin') {
            return true;
        }

        // Lanzamos una excepción con una respuesta personalizada si el usuario no está autorizado.
        throw new HttpResponseException(response()->json([
            'error' => 'No tienes permiso para eliminar clientes.'
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
