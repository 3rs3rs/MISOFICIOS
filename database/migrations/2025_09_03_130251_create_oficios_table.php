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
        Schema::create('oficios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero_unico')->unique()->nullable();
            $table->foreignId('remitente_id')
                ->constrained('departamentos')
                ->cascadeOnDelete();
            $table->foreignId('destinatario_id')
                ->constrained('departamentos')
                ->cascadeOnDelete();
            $table->foreignId('creador_id')
                ->constrained('users');
            $table->string('asunto')->nullable();
            $table->longText('cuerpo_html')->nullable();
            // ✅ NUEVO: indica si el oficio requiere contestación
            $table->boolean('requiere_respuesta')->default(false);
    
            // ✅ Si requiere respuesta, estos aplican. Si no, quedan NULL.
            $table->unsignedTinyInteger('plazo_dias')->nullable(); // 3–5 normalmente
            $table->date('fecha_limite')->nullable();
    
            $table->enum('estado', [
                'BORRADOR','ENVIADO','RECIBIDO','LEIDO','EN_RESPUESTA','RESPONDIDO','VENCIDO','CERRADO'
            ])->default('BORRADOR');
    
            $table->enum('prioridad', ['BAJA','NORMAL','ALTA','URGENTE'])->default('NORMAL');
    
            // 👇 Si ya usarás Spatie MediaLibrary, este campo puede quedar solo “legacy”
            $table->string('pdf_path')->nullable();
    
            // ✅ NUEVO: bandera para UI (aunque se puede inferir por media library)
            $table->boolean('tiene_adjuntos')->default(false);
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamp('fecha_recibido')->nullable();
            $table->foreignId('recibido_por')
                ->nullable()
                ->constrained('users');
            $table->timestamp('fecha_respuesta')->nullable();
            $table->foreignId('respondido_por')
                ->nullable()
                ->constrained('users');
            $table->enum('tipo',[
                'INFORMATIVO',
                'SOLICITUD',
                'RESPUESTA',
                'INVITACION',
                'CIRCULAR',
                'CONVOCATORIA',
                'OTRO'
                ])->default('INFORMATIVO');
            $table->string('hash_documento')->nullable(); 
            $table->timestamp('fecha_visto')->nullable();
            $table->foreignId('cerrado_por')
                ->nullable()
                ->constrained('users');
            $table->timestamp('fecha_cierre')->nullable();
            $table->string('motivo_cierre')->nullable();

            $table->softDeletes();
            $table->timestamps();
    
            // (Opcional) índices útiles:
            $table->index(['remitente_id', 'destinatario_id']);
            $table->index('destinatario_id');
            $table->index(['estado', 'prioridad']);
            $table->index('fecha_limite'); // MUY útil para vencidos

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficios');
    }
};
