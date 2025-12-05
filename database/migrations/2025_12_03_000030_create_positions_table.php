<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('type')->default('Full-time'); // Full-time, Part-time, Contract
            $table->string('location')->default('Remote'); // Remote, Hybrid, On-site
            $table->string('tags')->nullable(); // Comma-separated tags like "Laravel,AWS"
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // For ordering positions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};



