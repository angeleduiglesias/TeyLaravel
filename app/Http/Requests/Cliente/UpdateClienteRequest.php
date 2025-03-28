<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateClienteRequest extends FormRequest
{
    /**
     * Determinamos si el usuario está autorizado para hacer esta solicitud.
     *
     * En este ejemplo, se permite:
     * - Usuarios con rol "admin" actualizar cualquier registro.
     * - Usuarios con rol "cliente" solo actualizar su propio registro.
     * @return bool
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        // Si el usuario es administrador, puede actualizar cualquier cliente.
        if ($user->rol === 'admin') {
            return true;
        }

        // Si el usuario es cliente, verificamos que el cliente que se intenta actualizar corresponda con el cliente asociado a este usuario.
        if ($user->rol === 'cliente' && $user->cliente) {
            
            $cliente = $this->route('cliente');  // Obtenemos el cliente que se intenta actualizar.
            return $cliente && $cliente->id === $user->cliente->id;
        }

        // Si no cumple ninguna condición, se deniega el acceso y se le notifica.
        throw new HttpResponseException(response()->json([
            'error' => 'No tienes permiso para actualizar la información del cliente.'
        ], 403));
    }

    /**
     * En este ejemplo se validan los campos:
     * - dni: obligatorio, (exceptuando el registro actual).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Se asume que la ruta contiene el Cliente a actualizar.
        $clienteId = $this->route('cliente')->id;

        return [
            'dni'       => 'required|digits:8|unique:clientes,dni,' . $clienteId,
            'full_name'   => 'required|string|max:255',
            'telefono'  => 'required|digits:9',
        ];
    }
}
