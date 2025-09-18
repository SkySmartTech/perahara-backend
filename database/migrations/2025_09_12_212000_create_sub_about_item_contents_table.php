<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sub_about_item_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_about_item_id')->constrained('sub_about_items')->cascadeOnDelete();
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->string('image', 2048)->nullable(); // URL
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_about_item_contents');
    }
};

