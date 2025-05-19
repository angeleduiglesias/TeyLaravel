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
            $table->string('nombre_empresa')->nullable();
            $table->string('actividades')->nullable();
            $table->string('rubro');
            $table->enum('tipo_empresa',['SAC', 'EIRL']);
            $table->integer('numero_socios')->nullable();
            $table->enum('tipo_aporte',['dinero', 'bienes', 'mixto']);
            $table->string('rango_capital');
            
            $table->unsignedBigInteger('cliente_id')->unique();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
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
