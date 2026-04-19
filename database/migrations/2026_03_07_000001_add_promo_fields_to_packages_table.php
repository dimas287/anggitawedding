<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('promo_label')->nullable()->after('price');
            $table->text('promo_description')->nullable()->after('promo_label');
            $table->decimal('promo_discount_percent', 5, 2)->nullable()->after('promo_description');
            $table->date('promo_expires_at')->nullable()->after('promo_discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'promo_label',
                'promo_description',
                'promo_discount_percent',
                'promo_expires_at',
            ]);
        });
    }
};
