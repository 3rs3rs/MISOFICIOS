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
        Schema::create('quejas', function (Blueprint $table) {
            $table->biginteger('id')->unsigned()->autoIncrement();
            $table->foreignId('mpio_id')->constrained('mpios');
            $table->foreignId('departamento_id')->constrained('departamentos');
            $table->enum('tipo', ['Queja', 'Denuncia', 'Sugerencia', 'Reconocimiento']);
            $table->string('nombres', 30);
            $table->string('apellido_paterno', 30)->nullable();
            $table->string('apellido_materno', 30)->nullable();
            $table->string('domicilio', 50)->nullable();
            $table->string('colonia', 50)->nullable();
            $table->string('municipio', 50)->nullable();
            $table->string('num_telefono', 20);             // permite manejar números con guiones, paréntesis o espacios.
            $table->string('email');                        // Campo de correo.
            $table->enum('conoce_nombre', ['Si', 'No'])->default('No');
            $table->string('nombre_descripcion', 30);
            $table->date('fecha_hechos');
            $table->longText('descripcion_apliamente')->nullable();
            $table->longText('pruebas')->nullable();
            $table->string('adjuntar_pruebas', length: 200)->nullable();
            $table->longText('testigos')->nullable();
            $table->enum('anonimo', ['Si', 'No'])->default('Si');
            $table->dateTime('fecha_queja')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('activo', ['Si', 'No'])->default('Si');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quejas');
    }
};
