<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            //columnas de la tabla empresas primer formulario
            $table->string('nombre_empresa');
            $table->string('actividad_comercial');
            $table->enum('tipo_empresa',['sociedad anonima', 'sociedad resp limitada', 'empresario individual']);

            $table->unsignedBigInteger('cliente_id')->unique();
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
