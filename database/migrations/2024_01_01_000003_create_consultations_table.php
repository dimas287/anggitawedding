<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('consultation_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->date('preferred_date');
            $table->date('event_date')->nullable();
            $table->time('preferred_time');
            $table->enum('consultation_type', ['online', 'offline'])->default('offline');
            $table->text('message')->nullable();
            $table->text('meeting_notes')->nullable();
            $table->text('followup_notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'done', 'cancelled', 'converted'])->default('pending');
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
