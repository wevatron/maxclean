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
        Schema::create('precio_prendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('prenda_id')
                ->constrained('prendas')
                ->cascadeOnDelete();

            // Si en el futuro manejas precios por tienda
            $table->foreignId('sucursal_id')
                ->nullable()
                ->constrained('sucursals')
                ->nullOnDelete();

            $table->decimal('precio_normal', 8, 2);        // $6, $25, $90, etc
            $table->decimal('precio_express', 8, 2)->nullable(); // $10, $35, $25...
            $table->decimal('precio_paquete', 8, 2)->nullable(); // Ej: 150, 200, 230
            $table->integer('piezas_por_paquete')->nullable();   // Ej: 3

            $table->text('observaciones')->nullable(); // Ej: "Express aumenta $15 por pieza"

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precio_prendas');
    }
};
