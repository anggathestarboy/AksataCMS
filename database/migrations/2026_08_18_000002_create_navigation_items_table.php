<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()
                ->constrained('navigation_items')->cascadeOnDelete();
            $table->string('type'); // external | internal | page
            $table->string('url')->nullable();       // dipakai kalau type = external/internal
            $table->foreignId('page_id')->nullable() // dipakai kalau type = page
                ->constrained('pages')->nullOnDelete();
            $table->string('icon')->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
