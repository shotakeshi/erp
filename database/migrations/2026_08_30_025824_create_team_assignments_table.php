<?php

use App\Enums\TeamAssignmentType;
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
        Schema::create('team_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained()->restrictOnDelete();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->enum('type', [
                TeamAssignmentType::MEMBER->value,
                TeamAssignmentType::MANAGER->value,
            ])->default(TeamAssignmentType::MEMBER->value);
            $table->string('role', 50);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->nullable();
            $table->string('end_reason', 50)->nullable();
            $table->text('end_reason_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('ended_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'employee_id', 'is_current']);
            $table->index(['team_id', 'type', 'end_date']);
            $table->index(['employee_id', 'type', 'end_date']);
            $table->index(['team_id', 'employee_id', 'start_date', 'end_date'], 'idx_team_assignment');
            $table->index('role', 'idx_team_assignment_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_assignments');
    }
};
