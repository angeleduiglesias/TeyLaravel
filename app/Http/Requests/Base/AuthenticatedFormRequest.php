<?php

namespace App\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class AuthenticatedFormRequest extends FormRequest
{
    /**
     * Determina si el usuario está autenticado.
     */
    public function authorize(): bool
    {
        if (!$this->user()) {
            throw new HttpResponseException(response()->json([
                'error' => 'No estás autenticado.'
            ], 401));
        }

        return true; // deja que las subclases definan roles si es necesario
    }

    /**
     * Método auxiliar para verificar roles.
     */
    protected function authorizeRoles(array $roles): bool
    {
        $user = $this->user();

        // Verifica si el usuario tiene una relación "roles"
        if (method_exists($user, 'roles')) {
            $userRoles = $user->roles->pluck('name')->toArray();
        } else {
            $userRoles = [$user->rol];
        }

        if (array_intersect($roles, $userRoles)) {
            return true;
        }

        throw new HttpResponseException(response()->json([
            'error' => 'No tienes permisos para realizar esta acción.'
        ], 403));
    }
}
