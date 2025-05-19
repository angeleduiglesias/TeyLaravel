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
        Schema::create('posibles_nombres', function (Blueprint $table) {
            $table->id();
            $table->string('posible_nombre1');
            $table->string('posible_nombre2');
            $table->string('posible_nombre3');
            $table->string('posible_nombre4');

            $table->unsignedBigInteger('empresa_id')->unique();
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posibles_nombres');
    }
};
