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
        Schema::create('maquinas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sucursal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tipo_maquina_id')->constrained('tipo_maquinas')->cascadeOnDelete();

            $table->enum('status', ['libre', 'ocupada', 'fuera_de_servicio'])
                ->default('libre');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maquinas');
    }
};
