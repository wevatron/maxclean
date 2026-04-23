<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cortes_caja', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sucursal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // operador

            $table->date('fecha');
            $table->string('turno'); // matutino, vespertino, etc.

            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('total_efectivo', 10, 2)->default(0);
            $table->decimal('total_tarjeta', 10, 2)->default(0);
            $table->decimal('total_transferencia', 10, 2)->default(0);

            $table->timestamp('cerrado_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cortes_caja');
    }
};