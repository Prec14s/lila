<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->unsignedBigInteger('total');
            $table->string('payment_method')->nullable(); // qris / bank_transfer
            $table->string('payment_proof')->nullable();
            $table->enum('payment_status', ['waiting_payment', 'waiting_verification', 'rejected', 'approved'])
                ->default('waiting_payment');
            $table->enum('order_status', ['waiting', 'processing', 'completed'])->default('waiting');
            $table->text('note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('forwarded_to_kitchen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
