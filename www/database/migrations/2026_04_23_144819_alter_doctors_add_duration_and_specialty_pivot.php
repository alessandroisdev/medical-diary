<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Altera a Tabela Doctors
        Schema::table('doctors', function (Blueprint $table) {
            $table->integer('consultation_duration_minutes')->default(30)->after('crm');
        });

        // Tabela Pivot especialidades_medicos
        Schema::create('doctor_specialty', function (Blueprint $table) {
            $table->foreignUuid('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('specialty_id')->constrained()->cascadeOnDelete();
            $table->primary(['doctor_id', 'specialty_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_specialty');
        
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('specialty')->nullable();
            $table->dropColumn('consultation_duration_minutes');
        });
    }
};
