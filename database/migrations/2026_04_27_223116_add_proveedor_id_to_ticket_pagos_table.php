<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->foreignId('proveedor_id')
                ->nullable()
                ->after('ticket_id')
                ->constrained('proveedors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proveedor_id');
        });
    }
};