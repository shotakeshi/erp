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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')
                ->unique()
                ->after('id');

            $table->enum('status', ['active', 'inactive', 'onleave','terminated','blocked'])
                ->default('inactive')
                ->after('password');

            $table->timestamp('last_login_at')
                ->nullable()
                ->after('remember_token');

            $table->timestamp('activated_at')
                ->nullable()
                ->after('status');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            $table->dropColumn([
                'uuid',
                'status',
                'last_login_at',
                'created_by',
                'updated_by',
            ]);
        });
    }
};
