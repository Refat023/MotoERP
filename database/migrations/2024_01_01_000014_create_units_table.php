<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // kg, pcs, liter, meter, etc.
            $table->string('abbreviation')->unique(); // kg, pcs, L, m, etc.
            $table->text('description')->nullable();
            $table->decimal('conversion_factor', 8, 4)->default(1); // For unit conversion
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
