<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class ShowClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        // Si el usuario es cliente, verifica que el cliente en la ruta sea el suyo.
        if ($user->rol === 'cliente' && isset($user->cliente)) {
            $cliente = $this->route('cliente');
            return $cliente && $cliente->id === $user->cliente->id;
        }

        // En este caso, se deniega el acceso para el admin (o cualquier otro rol)
        throw new HttpResponseException(response()->json([
            'error' => 'No tienes permiso para ver los detalles de este cliente.'
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
