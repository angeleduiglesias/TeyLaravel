<?php

namespace App\Http\Requests\Tramite;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreTramiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // if(Auth::check() && Auth::user()->rol === 'admin') {
        //     return true;
        // }

        // throw new HttpResponseException(response()->json([
        //     'error' => 'No tienes permiso para acceder a esta información.'
        // ], 403));

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
            'estado'        => 'required|string|max:100',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date',
            'cliente_id'    => 'required|exists:clientes,id'
        ];
    }
}
