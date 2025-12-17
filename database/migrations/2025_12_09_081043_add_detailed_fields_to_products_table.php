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
        Schema::table('products', function (Blueprint $table) {
            $table->json('features')->nullable()->after('description');
            $table->json('benefits')->nullable()->after('features');
            $table->json('statistics')->nullable()->after('benefits');
            $table->string('button_text')->nullable()->after('url');
            $table->string('icon')->nullable()->after('button_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['features', 'benefits', 'statistics', 'button_text', 'icon']);
        });
    }
};
