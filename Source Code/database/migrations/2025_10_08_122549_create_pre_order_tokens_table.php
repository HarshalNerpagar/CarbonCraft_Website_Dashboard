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
        Schema::create('pre_order_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique(); // Unique token for URL
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade'); // Staff member who created token
            $table->string('payment_method')->default('upi'); // upi, razorpay, cash
            $table->decimal('total_amount', 10, 2)->nullable(); // Total order amount
            $table->decimal('advance_amount', 10, 2)->default(1000.00); // Advance amount collected
            $table->string('customer_phone')->nullable(); // Optional: Pre-fill if agent knows
            $table->string('customer_name')->nullable(); // Optional: Pre-fill if agent knows
            $table->boolean('used')->default(false); // Has customer filled the form?
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); // Created order ID
            $table->timestamp('expires_at')->nullable(); // Token expiry (24 hours)
            $table->timestamp('used_at')->nullable(); // When customer submitted form
            $table->text('notes')->nullable(); // Agent notes
            $table->timestamps();

            // Indexes for faster queries
            $table->index('token');
            $table->index('agent_id');
            $table->index('used');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_order_tokens');
    }
};
