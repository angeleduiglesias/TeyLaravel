<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Cliente;

class ReniecService
{
    public function consultarDni(string $dni)
    {
        $dni = Cliente::where('dni', $dni)->first();
        $response = Http::get('https://api.reniec.cloud/dni/'.$dni);
        return $response->json();
    }
}