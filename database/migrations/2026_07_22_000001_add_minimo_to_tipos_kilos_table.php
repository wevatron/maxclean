<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_kilos', function (Blueprint $table) {
            $table->decimal('minimo', 8, 2)->default(3)->after('precio');
        });

        DB::table('tipos_kilos')
            ->where('clave', 'ropa_interior')
            ->update(['minimo' => 1]);
    }

    public function down(): void
    {
        Schema::table('tipos_kilos', function (Blueprint $table) {
            $table->dropColumn('minimo');
        });
    }
};
