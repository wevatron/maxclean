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
        Schema::create('tipo_maquinas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');                         // Ej: Lavadora Chica
            $table->text('descripcion')->nullable();          // Opcional
            $table->integer('capacidad_kg')->nullable();      // Kg
            $table->integer('tiempo_minimo')->nullable();     // Minutos
            $table->integer('tiempo_maximo')->nullable();     // Minutos

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_maquinas');
    }
};
