<?php

namespace App\Http\Requests\Notario;

use Illuminate\Foundation\Http\FormRequest;

class IndexNotarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verifica si el usuario está autenticado
        if (!$this->user()) {
            throw new HttpResponseException(response()->json([
                'error' => 'No estás autenticado.'
            ], 401));
        }

        $user = auth()->user();

        if($user->rol === 'notario') {
            return true;
        }else{
            throw new HttpResponseException(response()->json([
                'error' => 'No tienes permiso correspondientes.'
            ], 403));
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
            //
        ];
    }
}
