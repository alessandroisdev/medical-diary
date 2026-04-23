<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('health_insurance_id')->nullable()->constrained()->nullOnDelete(); // Null significa Particular/Avulso
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            // Proteção p/ não cadastrar preço duas vezes para o mesmo convênio/médico
            // Pelo MySql, se health_insurance_id for Null, indices unicos ignoram. Mas vamos confiar na camada lógica pra unico nullable.
            // Para garantir:
            $table->unique(['doctor_id', 'health_insurance_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_prices');
    }
};
