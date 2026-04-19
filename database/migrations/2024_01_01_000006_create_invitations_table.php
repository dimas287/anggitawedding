<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('invitation_templates')->onDelete('set null');
            $table->string('slug')->unique();
            $table->string('groom_name');
            $table->string('bride_name');
            $table->string('groom_father')->nullable();
            $table->string('groom_mother')->nullable();
            $table->string('bride_father')->nullable();
            $table->string('bride_mother')->nullable();
            $table->datetime('akad_datetime')->nullable();
            $table->string('akad_venue')->nullable();
            $table->string('akad_address')->nullable();
            $table->datetime('reception_datetime')->nullable();
            $table->string('reception_venue')->nullable();
            $table->string('reception_address')->nullable();
            $table->string('maps_link')->nullable();
            $table->text('love_story')->nullable();
            $table->text('opening_quote')->nullable();
            $table->text('closing_message')->nullable();
            $table->string('hashtag')->nullable();
            $table->string('music_file')->nullable();
            $table->string('photo_prewedding')->nullable();
            $table->json('gallery_photos')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('rsvp_enabled')->default(true);
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
