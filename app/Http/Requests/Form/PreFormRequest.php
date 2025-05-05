<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class PreFormRequest extends FormRequest
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
            'tipo_aporte'  => 'required|string|max:255',
            'rango_capital'  => 'required|string|max:255',
            'rubro'  => 'required|string|max:255',
            'actividades'  => 'required|string|max:255',
            'tipo_empresa'  => 'required|string|max:255',
            'nombre_empresa'  => 'required|string|max:255',
            'posible_nombre1'  => 'required|string|max:255',
            'posible_nombre2'  => 'required|string|max:255',
            'posible_nombre3'  => 'required|string|max:255',
            'posible_nombre4'  => 'required|string|max:255',
            'numero_socios'  => 'nullable|integer|min:1|max:100',
            'dni'=> 'required|string|size:8|unique:clientes,dni',
            'nombre'  => 'required|string|max:255',
            'apellidos'  => 'required|string|max:255',
            'telefono' => 'required|digits:9',
            'email'    => 'required|email|unique:users,email',
        ];
    }
}