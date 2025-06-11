<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Base\AuthenticatedFormRequest;


class IndexClienteRequest extends AuthenticatedFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::authorize(); // Verifica autenticación

        return $this->authorizeRoles(['cliente']); // Verifica que tenga rol adecuado
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
