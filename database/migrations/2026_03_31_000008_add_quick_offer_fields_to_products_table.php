<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('quick_offer_enabled')->default(false)->after('black_friday_enabled');
            $table->unsignedTinyInteger('quick_offer_percentage')->default(0)->after('quick_offer_enabled');
            $table->timestamp('quick_offer_starts_at')->nullable()->after('quick_offer_percentage');
            $table->timestamp('quick_offer_ends_at')->nullable()->after('quick_offer_starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'quick_offer_enabled',
                'quick_offer_percentage',
                'quick_offer_starts_at',
                'quick_offer_ends_at',
            ]);
        });
    }
};
