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
        Schema::create('instagram_posts', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('instagram_url')->unique();
            $blueprint->string('media_path')->nullable(); // Saved image locally
            $blueprint->string('media_url')->nullable(); // Original IG media URL
            $blueprint->text('caption')->nullable();
            $blueprint->string('media_type')->default('image'); // image, video, carousel
            $blueprint->integer('sort_order')->default(0);
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_posts');
    }
};
