<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');
            $table->uuid('offline_id')->nullable();
            $table->enum('action', ['create', 'update', 'delete']);
            $table->enum('sync_status', ['success', 'conflict', 'failed']);
            $table->json('conflict_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->index(['organization_id', 'entity_type', 'entity_id']);
            $table->index('offline_id');
            $table->index('sync_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
