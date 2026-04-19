<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan index di kolom yang paling sering di-query untuk meningkatkan performa production.
     */
    public function up(): void
    {
        // invitations.slug — dipakai setiap halaman undangan publik dibuka
        Schema::table('invitations', function (Blueprint $table) {
            $table->index('slug', 'idx_invitations_slug');
            $table->index('is_published', 'idx_invitations_published');
        });

        // payments.payment_code — dipakai di webhook Midtrans
        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_code', 'idx_payments_code');
            $table->index(['booking_id', 'status'], 'idx_payments_booking_status');
        });

        // rsvps.invitation_id — dipakai untuk menghitung statistik RSVP
        Schema::table('rsvps', function (Blueprint $table) {
            $table->index('invitation_id', 'idx_rsvps_invitation');
            $table->index(['invitation_id', 'attendance'], 'idx_rsvps_attendance');
        });

        // chats — dipakai untuk polling pesan real-time
        Schema::table('chats', function (Blueprint $table) {
            $table->index(['booking_id', 'created_at'], 'idx_chats_booking_time');
        });

        // invitation_views — dipakai untuk cek view duplikat berdasarkan IP
        Schema::table('invitation_views', function (Blueprint $table) {
            $table->index(['invitation_id', 'ip_address', 'created_at'], 'idx_views_dedup');
        });

        // bookings — dipakai di dashboard user & admin
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_bookings_user_status');
            $table->index('payment_status', 'idx_bookings_payment_status');
        });
    }

    /**
     * Hapus semua index yang ditambahkan.
     */
    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex('idx_invitations_slug');
            $table->dropIndex('idx_invitations_published');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_code');
            $table->dropIndex('idx_payments_booking_status');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->dropIndex('idx_rsvps_invitation');
            $table->dropIndex('idx_rsvps_attendance');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex('idx_chats_booking_time');
        });

        Schema::table('invitation_views', function (Blueprint $table) {
            $table->dropIndex('idx_views_dedup');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_user_status');
            $table->dropIndex('idx_bookings_payment_status');
        });
    }
};
