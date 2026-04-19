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
        if (Schema::hasTable('portfolio_media_items')) {
            return;
        }

        Schema::create('portfolio_media_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_image_id')->constrained()->onDelete('cascade');
            $table->enum('media_type', ['image', 'video'])->default('image');
            $table->string('media_path')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_media_items');
    }
};
