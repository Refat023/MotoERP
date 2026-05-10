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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cash_register_session_id')->nullable()->constrained('cash_register_sessions')->onDelete('set null');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->enum('category', ['utilities', 'supplies', 'maintenance', 'marketing', 'other'])->default('other');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer'])->default('cash');
            $table->timestamp('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
