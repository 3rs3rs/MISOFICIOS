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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->biginteger('id')->unsigned()->autoIncrement();
            $table->foreignId('mpio_id')->constrained('mpios')->default('1');
            $table->string('nombre', 50)->nullable();
            $table->string('director', 50)->nullable();
            $table->string('tel_celular', 15)->nullable();      
            $table->string('clave', 10)->unique()->nullable();
            $table->string('email_contacto')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
