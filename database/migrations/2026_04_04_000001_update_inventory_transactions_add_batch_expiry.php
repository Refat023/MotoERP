<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->string('transaction_type')->default('adjustment')->after('type');
            $table->string('batch_number')->nullable()->after('reason');
            $table->date('expiry_date')->nullable()->after('batch_number');
            $table->string('reference')->nullable()->after('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_type', 'batch_number', 'expiry_date', 'reference']);
        });
    }
};
