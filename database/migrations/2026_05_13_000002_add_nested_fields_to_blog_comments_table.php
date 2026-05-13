<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('blog_comments', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('post_id')->constrained('blog_comments')->onDelete('cascade');
            }
            if (!Schema::hasColumn('blog_comments', 'likes')) {
                $table->integer('likes')->default(0)->after('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'likes']);
        });
    }
};
