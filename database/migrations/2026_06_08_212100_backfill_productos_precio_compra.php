<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('productos')
            ->whereNull('precio_compra')
            ->update([
                'precio_compra' => DB::raw('precio_base'),
            ]);
    }

    public function down(): void
    {
        DB::table('productos')
            ->whereNotNull('precio_compra')
            ->update([
                'precio_compra' => null,
            ]);
    }
};
