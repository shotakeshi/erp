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
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained('leave_types')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 8, 2);
            $table->text('reason');

            //pending, approved, rejected, cancelled
            $table->string('status')->default('pending');

            $table->text('rejection_reason')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('employees')
                ->restrictOnDelete();
            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('employees')
                ->restrictOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(
                ['employee_id', 'status'],
                'leave_requests_employee_status_idx'
            );

            $table->index(
                ['approved_by', 'status'],
                'leave_requests_approver_status_idx'
            );

            $table->index(
                ['employee_id', 'start_date', 'end_date'],
                'leave_requests_employee_dates_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
