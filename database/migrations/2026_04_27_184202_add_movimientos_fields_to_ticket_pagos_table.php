<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
        });

        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('ticket_id')
                ->nullable()
                ->change();

            $table->string('tipo_movimiento')
                ->default('venta')
                ->after('metodo_pago');

            $table->string('categoria')
                ->nullable()
                ->after('tipo_movimiento');

            $table->text('descripcion')
                ->nullable()
                ->after('categoria');
        });

        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->foreign('ticket_id')
                ->references('id')
                ->on('tickets')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
        });

        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_movimiento',
                'categoria',
                'descripcion',
            ]);

            $table->unsignedBigInteger('ticket_id')
                ->nullable(false)
                ->change();
        });

        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->foreign('ticket_id')
                ->references('id')
                ->on('tickets')
                ->cascadeOnDelete();
        });
    }
};