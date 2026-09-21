<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false)->after('role');
            });
        }

        DB::table('users')->where('role', 'admin')->update([
            'is_super_admin' => true,
            'role' => 'super_admin',
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'super_admin')->update([
            'role' => 'admin',
            'is_super_admin' => false,
        ]);

        if (Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_super_admin');
            });
        }
    }
};
