<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->boolean('cancelado')->default(false)->after('referencia');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_pagos', function (Blueprint $table) {
            $table->dropColumn('cancelado');
        });
    }
};