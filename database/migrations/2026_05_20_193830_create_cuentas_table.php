<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();

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

            $table->string('numero')->nullable()->index();

            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2)->default(0);
            $table->decimal('saldo', 10, 2)->default(0);

            $table->string('estatus')->default('abierta')->index();
            // abierta, parcial, pagada, cancelada

            $table->timestamp('abierta_en')->nullable();
            $table->timestamp('cerrada_en')->nullable();

            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};