<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_availabilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('specialty_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0 (Sun) to 6 (Sat)
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            
            // Um médico não pode ter duas escalas na mesma especialidade com o mesmo horário exato de início de bloco pro mesmo dia. O ideal seria checar sobreposição, mas deixamos as regras de negócio cuidarem.
            // Aqui adicionamos índice
            $table->index(['doctor_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_availabilities');
    }
};
