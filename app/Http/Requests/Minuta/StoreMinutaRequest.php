<?php

namespace App\Http\Requests\Minuta;

use Illuminate\Foundation\Http\FormRequest;

class StoreMinutaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::authorize();
        return $this->authorizeRoles(['cliente']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ''
        ];
    }
}
