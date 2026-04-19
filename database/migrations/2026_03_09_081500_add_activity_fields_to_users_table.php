<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_online_at')) {
                $table->timestamp('last_online_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'last_ip_address')) {
                $table->string('last_ip_address', 45)->nullable()->after('last_online_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_ip_address')) {
                $table->dropColumn('last_ip_address');
            }
            if (Schema::hasColumn('users', 'last_online_at')) {
                $table->dropColumn('last_online_at');
            }
        });
    }
};
