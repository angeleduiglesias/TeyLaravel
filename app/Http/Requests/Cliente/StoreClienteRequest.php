<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'dni'      => 'required|digits:8|unique:clientes,dni',
            'full_name'  => 'required|string|max:255',
            'telefono' => 'required|digits:9',
            'email'    => 'required|email|unique:users,email',
        ];
    }
}
