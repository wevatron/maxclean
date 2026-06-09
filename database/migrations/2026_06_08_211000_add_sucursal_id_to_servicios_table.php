<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->foreignId('sucursal_id')
                ->nullable()
                ->after('id')
                ->constrained('sucursals')
                ->nullOnDelete();

            $table->index(['sucursal_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropIndex(['sucursal_id', 'activo']);
            $table->dropConstrainedForeignId('sucursal_id');
        });
    }
};
