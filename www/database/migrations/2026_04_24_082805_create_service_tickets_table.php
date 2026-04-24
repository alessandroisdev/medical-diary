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
        Schema::create('service_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['priority', 'common'])->default('common');
            $table->string('number'); // C001, P002
            $table->enum('status', ['waiting', 'calling', 'attended', 'canceled'])->default('waiting');
            $table->uuid('collaborator_id')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('attended_at')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->foreign('collaborator_id')->references('id')->on('collaborators')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_tickets');
    }
};
