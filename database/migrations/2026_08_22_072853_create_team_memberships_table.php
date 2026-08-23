<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_memberships', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->nullable();
            $table->string('end_reason', 50)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('ended_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'employee_id', 'is_current']);
            $table->index(['team_id', 'end_date']);
            $table->index(['employee_id', 'end_date']);
            $table->index(['team_id', 'employee_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_memberships');
    }
};
