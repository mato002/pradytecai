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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('status')->default('new')->after('message');
            $table->text('admin_notes')->nullable()->after('status');
            $table->timestamp('read_at')->nullable()->after('admin_notes');
            $table->timestamp('responded_at')->nullable()->after('read_at');
            $table->foreignId('read_by')->nullable()->after('responded_at')->constrained('users')->nullOnDelete();
            $table->foreignId('responded_by')->nullable()->after('read_by')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['read_by']);
            $table->dropForeign(['responded_by']);
            $table->dropColumn(['status', 'admin_notes', 'read_at', 'responded_at', 'read_by', 'responded_by']);
        });
    }
};
