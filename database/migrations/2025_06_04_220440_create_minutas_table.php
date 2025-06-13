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
        Schema::create('minutas', function (Blueprint $table) {
            $table->id();
            // $table->string('nombre_cliente');
            $table->string('nacionalidad');
            $table->string('dni');
            $table->string('profesion');
            $table->string('estado_civil');
            $table->string('direccion');
            $table->string('nombre_conyuge')->nullable();
            $table->string('dni_conyuge')->nullable();

            // $table->string('nombre_empresa');
            $table->string('direccion_empresa');
            $table->string('provincia_empresa');
            $table->string('departamento_empresa');
            $table->string('objetivo');
            $table->integer('monto_capital');
            $table->string('apoderado')->nullable();
            $table->string('dni_apoderado')->nullable();
            $table->string('ciudad');
            $table->string('fecha_registro');
            $table->enum('tipo_formulario', ['eirlbnd', 'eirlbd', 'sacbnd', 'sacbd']);

            $table->unsignedBigInteger('documento_id')->nullable();
            $table->foreign('documento_id')->references('id')->on('documentos')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minutas');
    }
};
