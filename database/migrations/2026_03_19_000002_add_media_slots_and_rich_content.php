<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 2: Add media_slots to invitation_templates + media_files & extra content to invitations
return new class extends Migration
{
    public function up(): void
    {
        // Add media_slots JSON to invitation_templates
        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->json('media_slots')->nullable()->after('demo_slug');
        });

        // Add media_files (dynamic per-slot uploads) + extra content fields to invitations
        Schema::table('invitations', function (Blueprint $table) {
            $table->json('media_files')->nullable()->after('gallery_photos');
            // Structured love story (array of {year, title, description, photo_url})
            $table->json('love_story_items')->nullable()->after('love_story');
            // Bank accounts for gift
            $table->json('bank_accounts')->nullable()->after('closing_message');
            $table->string('qris_image')->nullable()->after('bank_accounts');
            // Groom/Bride profiles
            $table->string('groom_photo')->nullable()->after('photo_prewedding');
            $table->string('bride_photo')->nullable()->after('groom_photo');
            $table->string('groom_instagram')->nullable()->after('bride_photo');
            $table->string('bride_instagram')->nullable()->after('groom_instagram');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->dropColumn('media_slots');
        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn([
                'media_files', 'love_story_items', 'bank_accounts', 'qris_image',
                'groom_photo', 'bride_photo', 'groom_instagram', 'bride_instagram',
            ]);
        });
    }
};
