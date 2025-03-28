<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class FirebaseAuthService
{
    protected Client $client;
    protected string $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.firebase.api_key');
    }

    /**
     * Creamos un usuario en Firebase con un correo y contraseña predeterminada.
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function createUser(string $email, string $password = 'DitechPeru2025'): array
    {
        $url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key={$this->apiKey}";

        $response = $this->client->post($url, [
            'json' => [
                'email'             => $email,
                'password'          => $password,
                'returnSecureToken' => true,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['error'])) {
            throw new Exception('Error al crear el usuario en Firebase: ' . json_encode($data['error']));
        }

        return $data;
    }

    /**
     * Envía el correo de restablecimiento de contraseña al usuario.
     *
     * @param string $email
     * @return array
     */
    public function sendPasswordResetEmail(string $email): array
    {
        $url = "https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key={$this->apiKey}";

        $response = $this->client->post($url, [
            'json' => [
                'requestType' => 'PASSWORD_RESET',
                'email'       => $email,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['error'])) {
            throw new Exception('Error al enviar el correo de restablecimiento: ' . json_encode($data['error']));
        }

        return $data;
    }
}
