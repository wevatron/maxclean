<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->foreignId('cuenta_id')
                ->nullable()
                ->after('ticket_id')
                ->constrained('cuentas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('cuenta_pago_id')
                ->nullable()
                ->after('cuenta_id')
                ->constrained('cuenta_pagos')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cuenta_pago_id');
            $table->dropConstrainedForeignId('cuenta_id');
        });
    }
};