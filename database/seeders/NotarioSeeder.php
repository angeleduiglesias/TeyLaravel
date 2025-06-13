<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notario;
use App\Services\FirebaseAuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class NotarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('NOTARIO_EMAIL');
        $password = env('NOTARIO_PASSWORD');

        if (User::where('email', $email)->exists()) {
            $this->command->info("El usuario notario ya existe.");
            return;
        }

        $firebaseAuthService = app(FirebaseAuthService::class);

        try {
            $firebaseAuthService->createUser($email, $password);
            $this->command->info("Usuario notario creado en Firebase.");
        } catch (Exception $e) {
            $this->command->error("Error al crear el usuario en Firebase: " . $e->getMessage());
            return;
        }

        // Crear usuario en base de datos
        $user = User::create([
            'email'    => $email,
            'password' => Hash::make($password),
            'rol'      => 'notario',
            'remember_token' => Str::random(60),
        ]);

        // Crear datos del notario
        $notario = new Notario();
        $notario->nombre = env('NOTARIO_NOMBRES', 'Juan');
        $notario->apellidos = env('NOTARIO_APELLIDOS', 'Pérez');
        $notario->telefono = env('NOTARIO_TELEFONO', '123456789');
        $notario->direccion = env('NOTARIO_DIRECCION', 'Av. Ejemplo 123');
        $notario->user_id = $user->id;
        $notario->save();

        $this->command->info("Usuario notario registrado localmente.");

        try {
            $firebaseAuthService->sendPasswordResetEmail($email);
            $this->command->info("Correo de restablecimiento de contraseña enviado.");
        } catch (Exception $e) {
            $this->command->error("Error al enviar el correo de restablecimiento: " . $e->getMessage());
        }
    }
}
