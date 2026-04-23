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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable();
            $table->uuid('appointment_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['income', 'expense'])->default('income');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // pix, credit_card, cash
            $table->string('gateway')->default('local'); // asaas, pagarme, mercadopago, paypal, stripe, local
            $table->string('gateway_id')->nullable(); // ID da transação no gateway
            $table->date('due_date')->nullable();
            $table->dateTime('paid_at')->nullable();
            
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
