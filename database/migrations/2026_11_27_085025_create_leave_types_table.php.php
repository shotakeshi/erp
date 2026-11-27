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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            
            $table->boolean('is_paid')->default(true);
            $table->boolean('allow_half_day')->default(false);
            $table->boolean('deduct_from_balance')->default(true);
            
            $table->boolean('is_carry_forward')->default(false);
            $table->decimal('max_carry_forward', 5, 2)->default(0);
            $table->decimal('max_days_per_year', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
