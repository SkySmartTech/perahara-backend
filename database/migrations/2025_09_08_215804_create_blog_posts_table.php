<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('title');
            $table->text('short_description');
            $table->longText('content');
            $table->string('image_url', 2048)->nullable();
            $table->enum('status', ['pending', 'published', 'unpublished'])
                  ->default('pending');

            // Meta fields
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
