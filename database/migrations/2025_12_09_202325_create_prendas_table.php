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
        Schema::create('prendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_prenda_id')
                ->constrained('categoria_prendas')
                ->cascadeOnDelete();

            $table->string('nombre');                   // Ej: Toalla de baño, Edredón Queen
            $table->string('tamano')->nullable();       // normal, delgado, jumbo, o null
            $table->string('unidad')->default('pieza'); // pieza / kg / paquete
            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prendas');
    }
};
