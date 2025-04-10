<?php

namespace App\Http\Requests\Notario;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verificamos si el usuario está autenticado.
        if (!auth()->check()) {
            throw new HttpResponseException(response()->json([
                'error' => 'No estás autenticado.'
            ], 401));
        }
        
        // Obtenemos el usuario autenticado.
        $user = auth()->user();

        // Si el usuario es notario, puede actualizar sus datos.
        if ($user->rol === 'notario') {
            return true;
        } 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'required|digits:9',
            'direccion' => 'required|string|max:255',
        ];
    }
}
