<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add new columns after existing ones
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null')->after('category');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('set null')->after('category_id');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null')->after('sub_category_id');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null')->after('brand_id');
            
            // Multiple pricing fields
            $table->decimal('mrp', 10, 2)->nullable()->comment('Maximum Retail Price')->after('price');
            $table->decimal('wholesale_price', 10, 2)->nullable()->comment('Wholesale/Cost price')->after('mrp');
            $table->decimal('offer_price', 10, 2)->nullable()->comment('Special offer price')->after('wholesale_price');
            
            // Pricing strategy
            $table->enum('pricing_type', ['fixed', 'tiered'])->default('fixed')->after('offer_price');
            
            // Additional product info
            $table->string('barcode')->nullable()->unique()->after('sku');
            $table->decimal('weight', 8, 2)->nullable()->after('barcode');
            $table->text('specifications')->nullable()->after('weight');
            
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('unit_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignKeyIfExists('products_category_id_foreign');
            $table->dropForeignKeyIfExists('products_sub_category_id_foreign');
            $table->dropForeignKeyIfExists('products_brand_id_foreign');
            $table->dropForeignKeyIfExists('products_unit_id_foreign');
            
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_brand_id_index');
            $table->dropIndex('products_unit_id_index');
            
            $table->dropColumn([
                'category_id',
                'sub_category_id',
                'brand_id',
                'unit_id',
                'mrp',
                'wholesale_price',
                'offer_price',
                'pricing_type',
                'barcode',
                'weight',
                'specifications'
            ]);
        });
    }
};
