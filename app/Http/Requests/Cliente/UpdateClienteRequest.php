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
        parent::authorize();
        return $this->authorizeRoles(['admin', 'cliente']);
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
            'nombre'   => 'required|string|max:255',
            'apellidos'   => 'required|string|max:255',
            'telefono'  => 'required|digits:9',
        ];
    }
}
