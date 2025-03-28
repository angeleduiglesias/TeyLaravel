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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
            $table->decimal('monto', 10, 2);
            $table->dateTime('fecha');
            $table->string('comprobante')->nullable();
            $table->enum('tipo_pago', ['reserva_nombre', 'llenado_minuta']);
            
            $table->unsignedBigInteger('tramite_id');
            $table->foreign('tramite_id')->references('id')->on('tramites');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
