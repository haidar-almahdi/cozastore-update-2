<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('size_guides', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('scope_type', ['product', 'category']);
            $table->unsignedBigInteger('scope_id');
            $table->string('gender')->nullable(); // men, women, unisex
            $table->enum('unit', ['cm', 'inch'])->default('cm');
            $table->json('table_schema')->nullable(); // columns meta
            $table->json('rows')->nullable(); // table rows
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['scope_type','scope_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_guides');
    }
};
