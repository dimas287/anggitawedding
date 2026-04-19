<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('restrict');
            $table->string('groom_name');
            $table->string('bride_name');
            $table->date('event_date');
            $table->string('venue');
            $table->text('venue_address')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->integer('estimated_guests')->nullable();
            $table->text('notes')->nullable();
            $table->text('consultation_preference')->nullable();
            $table->decimal('package_price', 15, 2);
            $table->decimal('dp_amount', 15, 2);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->enum('status', ['pending', 'dp_paid', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'dp_paid', 'partially_paid', 'paid_full'])->default('unpaid');
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
