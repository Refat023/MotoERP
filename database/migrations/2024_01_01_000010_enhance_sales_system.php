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
        // Add columns to sales table if they don't exist
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(15)->after('tax');
            }
            if (!Schema::hasColumn('sales', 'held_at')) {
                $table->timestamp('held_at')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('sales', 'item_discount_total')) {
                $table->decimal('item_discount_total', 10, 2)->default(0)->after('discount');
            }
        });

        // Add columns to sale_items table
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'item_discount')) {
                $table->decimal('item_discount', 10, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('sale_items', 'item_discount_type')) {
                $table->enum('item_discount_type', ['fixed', 'percent'])->default('fixed')->after('item_discount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('tax_rate', 'held_at', 'item_discount_total');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('item_discount', 'item_discount_type');
        });
    }
};
