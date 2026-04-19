<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->string('preview_image')->nullable();
            $table->enum('theme', ['classic', 'modern', 'rustic', 'floral', 'minimalist', 'royal', 'bohemian', 'garden']);
            $table->string('primary_color')->default('#D4AF37');
            $table->string('secondary_color')->default('#FFFFFF');
            $table->string('font_family')->default('Playfair Display');
            $table->boolean('has_music')->default(false);
            $table->string('default_music')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_templates');
    }
};
