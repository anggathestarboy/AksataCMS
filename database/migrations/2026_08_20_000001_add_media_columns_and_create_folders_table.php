<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('folders')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('media', function (Blueprint $table) {
            $table->unsignedInteger('width')->nullable()->after('path');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->string('loading', 10)->default('lazy')->after('height');
            $table->string('slug')->nullable()->after('loading');
            $table->foreignId('folder_id')->nullable()->after('slug')->constrained('folders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn(['width', 'height', 'loading', 'slug', 'folder_id']);
        });

        Schema::dropIfExists('folders');
    }
};
