<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'bank', 'digital', 'check']);
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('payment_date');
            $table->string('reference_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('sync_status', ['synced', 'pending', 'conflict'])->default('synced');
            $table->uuid('offline_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('invoice_id');
            $table->index('payment_date');
            $table->index('offline_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
