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
        Schema::create('login_tokens', function (Blueprint $table) {
            // Usuario al que pertenece el QR
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Token único que irá en el QR
            $table->string('token', 64)->unique();

            // Fecha de expiración (ej. now() + 5 min)
            $table->timestamp('expires_at');

            // Marca cuando ya fue usado
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            // Índices útiles
            $table->index(['token', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_tokens');
    }
};
