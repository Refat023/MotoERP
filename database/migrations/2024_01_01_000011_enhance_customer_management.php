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
        // Add columns to customers table
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('name');
            }
            if (!Schema::hasColumn('customers', 'registration_date')) {
                $table->timestamp('registration_date')->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'loyalty_points')) {
                $table->integer('loyalty_points')->default(0)->after('total_purchases');
            }
            if (!Schema::hasColumn('customers', 'points_redeemed')) {
                $table->integer('points_redeemed')->default(0)->after('loyalty_points');
            }
            if (!Schema::hasColumn('customers', 'credit_limit')) {
                $table->decimal('credit_limit', 10, 2)->default(0)->after('loyalty_points');
            }
            if (!Schema::hasColumn('customers', 'credit_balance')) {
                $table->decimal('credit_balance', 10, 2)->default(0)->after('credit_limit');
            }
            if (!Schema::hasColumn('customers', 'tier')) {
                $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('credit_balance');
            }
        });

        // Create customer transactions table
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['purchase', 'payment', 'loyalty_earned', 'loyalty_redeemed', 'credit_issued', 'credit_applied'])->default('purchase');
            $table->decimal('amount', 10, 2);
            $table->integer('loyalty_points')->default(0);
            $table->text('description')->nullable();
            $table->string('reference_id')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('type');
            $table->index('created_at');
        });

        // Create loyalty program rules table
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum']);
            $table->decimal('min_purchase', 10, 2);
            $table->decimal('max_purchase', 10, 2)->nullable();
            $table->decimal('points_multiplier', 5, 2)->default(1); // e.g., 1 peso = 1 point
            $table->integer('discount_percentage')->default(0);
            $table->text('benefits')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_programs');
        Schema::dropIfExists('customer_transactions');
        
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('loyalty_points', 'points_redeemed', 'credit_limit', 'credit_balance', 'registration_date', 'tier', 'tax_id', 'contact_person');
        });
    }
};
