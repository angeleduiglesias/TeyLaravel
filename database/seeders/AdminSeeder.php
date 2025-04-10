<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\FirebaseAuthService;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (User::where('email', $email)->exists()) {
            $this->command->info("El usuario administrador ya existe.");
            return;
        }

        $firebaseAuthService = app(FirebaseAuthService::class);

        try {
            $firebaseAuthService->createUser($email, $password);
            $this->command->info("Usuario administrador creado en Firebase.");
        } catch (Exception $e) {
            $this->command->error("Error al crear el usuario en Firebase: " . $e->getMessage());
            return;
        }

        // Crear usuario en base de datos
        $user = User::create([
            'email'    => $email,
            'password' => Hash::make($password),
            'rol'      => 'admin',
        ]);

        // Crear datos del admin en tabla admins
        $admin = new Admin();
        $admin->nombres = env('ADMIN_NOMBRES', 'Admin');
        $admin->apellidos = env('ADMIN_APELLIDOS', 'General');
        $admin->user_id = $user->id;
        $admin->save();

        $this->command->info("Usuario administrador registrado localmente.");

        try {
            $firebaseAuthService->sendPasswordResetEmail($email);
            $this->command->info("Correo de restablecimiento de contraseña enviado.");
        } catch (Exception $e) {
            $this->command->error("Error al enviar el correo de restablecimiento: " . $e->getMessage());
        }
    }
}
