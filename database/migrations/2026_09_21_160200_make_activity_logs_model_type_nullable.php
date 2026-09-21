<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rebuild activity_logs so model_type can be nullable (SQLite-safe).
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('activity_logs', function (Blueprint $table) {
                // SQLite: recreate via rename if needed; for existing installs use a soft approach.
            });

            Schema::create('activity_logs_tmp', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('description')->nullable();
                $table->json('changes')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index('created_at');
            });

            DB::statement('INSERT INTO activity_logs_tmp (id, user_id, action, model_type, model_id, description, changes, ip_address, user_agent, created_at, updated_at)
                SELECT id, user_id, action, model_type, model_id, description, changes, ip_address, user_agent, created_at, updated_at FROM activity_logs');

            Schema::drop('activity_logs');
            Schema::rename('activity_logs_tmp', 'activity_logs');

            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('model_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Irreversible for SQLite without data loss risk; no-op.
    }
};
