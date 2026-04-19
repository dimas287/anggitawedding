<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'includes_digital_invitation')) {
                $table->boolean('includes_digital_invitation')->default(true)->after('features');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'includes_digital_invitation')) {
                $table->dropColumn('includes_digital_invitation');
            }
        });
    }
};
