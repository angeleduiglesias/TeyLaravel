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
        Schema::create('socios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nacionalidad');
            $table->string('dni');
            $table->string('profesion');
            $table->enum('estado_civil',['soltero', 'casado', 'divorciado', 'viudo']);
            $table->string('nombre_conyuge')->nullable();
            $table->string('dni_conyuge')->nullable();

            $table->unsignedBigInteger('minuta_id')->nullable();
            $table->foreign('minuta_id')->references('id')->on('minutas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socios');
    }
};
