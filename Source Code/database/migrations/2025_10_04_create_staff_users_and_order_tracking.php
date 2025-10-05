<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new fields to orders table for better source tracking
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_channel')->nullable()->after('order_source')
                  ->comment('website, whatsapp, phone, instagram, walk-in, etc.');
            $table->integer('assigned_to')->nullable()->after('created_by')
                  ->comment('Staff member assigned to handle this order');
            
            $table->index('order_channel');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_channel']);
            $table->dropIndex(['assigned_to']);
            $table->dropColumn(['order_channel', 'assigned_to']);
        });
    }
};
