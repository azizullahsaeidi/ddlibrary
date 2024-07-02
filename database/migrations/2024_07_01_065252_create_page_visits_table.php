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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('page_url', 255);
            $table->string('user_agent', 255);
            $table->string('ip_address', 45);
            $table->string('browser', 45);
            $table->string('title')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->string('language', 4);
            $table->string('gender', 11)->index()->nullable();
            $table->foreignId('page_type_id')->constrained('page_types');
            $table->foreignId('device_id')->constrained('devices');
            $table->foreignId('platform_id')->constrained('platforms');
            $table->foreignId('browser_id')->constrained('browsers');
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};