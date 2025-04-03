<?php

namespace App\Http\Requests\Notario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;


class StoreNotarioRequest extends FormRequest
{
    /**
     * Evaluamos si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Verificamos si el usuario está autenticado y si su rol es "admin".
        if (Auth::check() && Auth::user()->rol === 'admin') {
            return true;
        }

        throw new HttpResponseException(response()->json([
            'error' => 'No tienes permiso para acceder a esta información.'
        ], 403));
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'required|digits:9',
            'direccion' => 'required|string|max:255',
        ];
    }
}
