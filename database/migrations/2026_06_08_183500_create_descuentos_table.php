<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->id();
            $table->date('inicio')->nullable()->index();
            $table->date('fin')->nullable()->index();
            $table->decimal('porcentaje', 8, 2)->nullable();
            $table->decimal('fijo', 10, 2)->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->enum('nivel', ['personal', 'global'])->default('global')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descuentos');
    }
};
