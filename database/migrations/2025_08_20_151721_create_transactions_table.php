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
            $table->id();
            $table->string('transaction_id')->default('');
            $table->string('transaction_code')->default('');
            $table->decimal('amount',12,2)->default(0);
            $table->string('account_number')->default('');
            $table->string('customer_name')->default('');
            $table->string('phone_number')->default('');
            $table->string('status')->default('Success');
            $table->string('narrative')->default('');
            $table->string('ftCr_narration')->default('');
            $table->string('payment_details')->default('');
            $table->string('credit_reference')->default('');
            $table->string('currency')->default('KES');
            $table->timestamp('transaction_date')->default(null);
            $table->timestamps();
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
