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
        Schema::create('order_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
            $table->string('type'); // 'image' or 'audio'
            $table->string('file_path'); // Storage path
            $table->string('original_name'); // Original filename
            $table->string('mime_type'); // File MIME type
            $table->integer('file_size'); // Size in bytes
            $table->string('uploaded_by')->default('customer'); // 'customer' or 'admin'
            $table->timestamps();

            $table->index('order_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_attachments');
    }
};
