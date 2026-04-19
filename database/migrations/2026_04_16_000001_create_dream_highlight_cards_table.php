<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dream_highlight_cards', function (Blueprint $table) {
            $table->id();
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('quote')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dream_highlight_cards');
    }
};
