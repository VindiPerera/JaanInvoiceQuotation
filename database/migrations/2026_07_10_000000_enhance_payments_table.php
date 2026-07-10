<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Add indexes for better query performance
            $table->index('payment_method');
            $table->index(['invoice_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeignIdFor('created_by');
            $table->dropForeignIdFor('updated_by');
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['invoice_id', 'payment_date']);
        });
    }
};
