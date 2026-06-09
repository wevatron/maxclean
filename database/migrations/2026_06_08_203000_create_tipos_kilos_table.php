<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_kilos', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });

        DB::table('tipos_kilos')->insert([
            [
                'clave' => 'basico',
                'nombre' => 'Básico',
                'descripcion' => 'Lavado estándar por kilo',
                'precio' => 22,
                'activo' => true,
                'orden' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'premium',
                'nombre' => 'Premium',
                'descripcion' => 'Lavado premium por kilo',
                'precio' => 28,
                'activo' => true,
                'orden' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'extra_lavado',
                'nombre' => 'Extra lavado',
                'descripcion' => 'Lavado intensivo por kilo',
                'precio' => 32,
                'activo' => true,
                'orden' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'expres',
                'nombre' => 'Expres',
                'descripcion' => 'Servicio express por kilo',
                'precio' => 30,
                'activo' => true,
                'orden' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'ropa_interior',
                'nombre' => 'Ropa interior',
                'descripcion' => 'Lavado especial por kilo',
                'precio' => 25,
                'activo' => true,
                'orden' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_kilos');
    }
};
