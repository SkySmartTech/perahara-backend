<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sub_about_item_content_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_about_item_content_id')
                  ->constrained('sub_about_item_contents')
                  ->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_about_item_content_details');
    }
};

