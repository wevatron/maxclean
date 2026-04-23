<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {

            $table->foreignId('user_id')
                ->nullable()
                ->after('ticket_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('sucursal_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn(['user_id', 'sucursal_id']);
        });
    }
};