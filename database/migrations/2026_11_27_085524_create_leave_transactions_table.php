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
        Schema::create('leave_transactions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained('leave_types')
                ->restrictOnDelete();

            $table->foreignId('application_id')
                ->nullable()
                ->constrained('leave_applications')
                ->restrictOnDelete();

            $table->foreignId('reversal_of_id')
                ->nullable()
                ->unique()
                ->constrained('leave_transactions')
                ->restrictOnDelete();

            // grant | bonus | accrual | usage | adjustment | reversal
            $table->string('transaction_type', 50);
            $table->decimal('days', 8, 2);
            $table->char('period', 7)->nullable();
            $table->text('note')->nullable();

            // NULL for system jobs
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();
            $table->unique(
                ['employee_id', 'leave_type_id', 'transaction_type', 'period'],
                'leave_transactions_accrual_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_transactions');
    }
};
