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
        Schema::table('tickets', function (Blueprint $table) {
            $table->boolean('modo_por_kilo')->default(false)->after('tipo');
            $table->decimal('kilos', 8, 2)->nullable()->after('modo_por_kilo');
            $table->string('tipo_lavado_kilo')->nullable()->after('kilos');
            $table->decimal('precio_kilo', 8, 2)->nullable()->after('tipo_lavado_kilo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['modo_por_kilo', 'kilos', 'tipo_lavado_kilo', 'precio_kilo']);
        });
    }
};
