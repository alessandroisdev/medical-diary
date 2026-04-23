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
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id');
            $table->uuid('doctor_id');
            $table->uuid('collaborator_id')->nullable();
            $table->dateTime('scheduled_at');
            $table->enum('status', ['scheduled', 'confirmed', 'arrived', 'in_consultation', 'finished', 'canceled', 'no_show'])->default('scheduled');
            $table->string('consultation_type')->default('routine');
            $table->text('notes')->nullable();
            
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('restrict');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('restrict');
            $table->foreign('collaborator_id')->references('id')->on('collaborators')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
