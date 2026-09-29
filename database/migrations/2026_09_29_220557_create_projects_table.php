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
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->enum('project_type', [
                'development',
                'maintenance',
                'internal',
                'support',
            ])->default('development');
            $table->text('description')->nullable();
            $table->string('project_avatar')->nullable();
            $table->enum('status', [
                'planning',
                'active',
                'on_hold',
                'completed',
                'cancelled',
            ])->default('planning');
            $table->enum('priority', [
                'low',
                'normal',
                'high',
                'critical',
            ])->default('normal');
            $table->date('start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('project_url', 2048)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
