<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_comments', function (Blueprint $col) {
            $col->id();
            $col->foreignId('post_id')->constrained()->onDelete('cascade');
            $col->foreignId('parent_id')->nullable()->constrained('blog_comments')->onDelete('cascade');
            $col->string('name');
            $col->string('email');
            $col->text('content');
            $col->integer('likes')->default(0);
            $col->boolean('is_approved')->default(false);
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
