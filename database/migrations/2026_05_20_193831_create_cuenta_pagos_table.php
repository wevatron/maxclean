<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cuenta_id')
                ->constrained('cuentas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

$table->foreignId('cliente_id')
    ->constrained('users')
    ->cascadeOnUpdate()
    ->restrictOnDelete();

            $table->foreignId('sucursal_id')
                ->constrained('sucursals')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->decimal('monto', 10, 2)->default(0);

            $table->string('metodo_pago')->default('efectivo');
            // efectivo, tarjeta, transferencia

            $table->string('referencia')->nullable();

            $table->boolean('cancelado')->default(false);
            $table->timestamp('cancelado_en')->nullable();
            $table->foreignId('cancelado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_pagos');
    }
};