<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Inicia sesión, valida credenciales y redirige según el rol.
     */
    public function login(Request $request)
    {
        // Validar campos del request.
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Buscar usuario por correo.
        $user = User::where('email', $request->email)->first();

        // Si el usuario no existe o la contraseña es incorrecta.
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Generar token de autenticación (aquí usamos Sanctum).
        $token = $user->createToken('api-token')->plainTextToken;

        // Determinar la URL de redirección según el rol del usuario.
        $redirectUrl = $this->getRedirectUrl($user->rol);

        return response()->json([
            'message'      => 'Inicio de sesión exitoso',
            'token'        => $token,
            'user'         => $user,
            'redirect_url' => $redirectUrl
        ]);
    }

    /**
     * Cierra la sesión y revoca el token de autenticación.
     */
    public function logout(Request $request)
    {
        // Revocar todos los tokens del usuario.
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

    /**
     * Obtiene la URL de redirección según el rol del usuario.
     */
    private function getRedirectUrl(string $rol): string
    {
        return match ($rol) {
            'admin'   => '/admin/dashboard',
            'notario' => '/notario/dashboard',
            'cliente' => '/cliente/dashboard',
            default   => '/login',
        };
    }
}
