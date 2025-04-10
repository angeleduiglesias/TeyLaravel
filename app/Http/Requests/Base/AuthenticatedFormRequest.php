<?php

namespace App\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class AuthenticatedFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!$this->user()) {
            throw new HttpResponseException(response()->json([
                'error' => 'No estás autenticado.'
            ], 401));
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        // Normalizamos roles requeridos como array
        $roles = is_array($roles) ? $roles : [$roles];

        // Si el usuario tiene una relación de roles (array)
        if (method_exists($user, 'roles')) {
            $userRoles = $user->roles->pluck('name')->toArray(); // Ej. ['admin', 'notario']
        } else {
            $userRoles = [$user->rol]; // Para usuarios con un solo campo 'rol'
        }

        if (array_intersect($roles, $userRoles)) {
            return true;
        }

        throw new HttpResponseException(response()->json([
            'error' => 'No tienes permisos para realizar esta acción.'
        ], 403));
    }
}
