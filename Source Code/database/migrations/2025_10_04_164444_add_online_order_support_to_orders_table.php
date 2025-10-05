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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_source')->default('pos')->after('order_type')->comment('pos or online');
            $table->json('customization_data')->nullable()->after('note')->comment('Full order form data from website');
            $table->string('razorpay_order_id')->nullable()->after('customization_data');
            $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            $table->string('razorpay_signature')->nullable()->after('razorpay_payment_id');
            $table->string('payment_method_type')->nullable()->after('razorpay_signature')->comment('full-payment, installments, cod');
            $table->boolean('needs_pickup')->default(false)->after('payment_method_type');

            // Add indexes for faster queries
            $table->index('order_source');
            $table->index('razorpay_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_source']);
            $table->dropIndex(['razorpay_payment_id']);
            $table->dropColumn([
                'order_source',
                'customization_data',
                'razorpay_order_id',
                'razorpay_payment_id',
                'razorpay_signature',
                'payment_method_type',
                'needs_pickup'
            ]);
        });
    }
};
