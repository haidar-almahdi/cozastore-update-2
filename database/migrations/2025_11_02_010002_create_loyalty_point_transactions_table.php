<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points'); // positive for award, negative for redeem
            $table->string('type'); // e.g., comment_award, order_award, redeem
            $table->nullableMorphs('reference'); // reference_type, reference_id
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });

        // Optional: unique composite to prevent duplicates per reference
        Schema::table('loyalty_point_transactions', function (Blueprint $table) {
            $table->unique(['user_id', 'type', 'reference_type', 'reference_id'], 'uniq_user_type_ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_transactions');
    }
};
