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
        Schema::create('respuestas', function (Blueprint $table) {
            $table->biginteger('id')->unsigned()->autoIncrement();
            $table->foreignId('oficio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('autor_id')->constrained('users');
            $table->text('cuerpo_html')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('fecha_respuesta')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas');
    }
};
