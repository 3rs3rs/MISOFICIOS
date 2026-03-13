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
        Schema::create('mpios', function (Blueprint $table) {
            $table->biginteger('id')->unsigned()->autoIncrement();
            $table->string('nombre');
            $table->string('logotipo', length: 200)->nullable();
            $table->enum('activo', ['Si', 'No'])->default('Si');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpios');
    }
};