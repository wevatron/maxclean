<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('puntos', function (Blueprint $table) {
            $table->id();

            // Cliente que recibe los puntos
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Usuario que asignó los puntos
            $table->foreignId('asignado_por')->constrained('users');

            $table->integer('puntos'); // cantidad
            $table->dateTime('fecha')->nullable(); // fecha asignación

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos');
    }
};
