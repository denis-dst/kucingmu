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
        // 1. Social Posts Table
        if (!Schema::hasTable('social_posts')) {
            Schema::create('social_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('category')->default('general'); // general, healthEducation, showcaseKtam, strayRescue, feedingStation
                $table->text('caption')->nullable();
                $table->foreignId('tagged_cat_id')->nullable()->constrained('cats')->nullOnDelete();
                $table->string('location')->nullable();
                $table->unsignedInteger('likes_count')->default(0);
                $table->unsignedInteger('comments_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Social Post Media Table (Multiple photos/carousel)
        if (!Schema::hasTable('social_post_media')) {
            Schema::create('social_post_media', function (Blueprint $table) {
                $table->id();
                $table->foreignId('social_post_id')->constrained()->cascadeOnDelete();
                $table->string('media_path');
                $table->string('media_type')->default('image'); // image, video
                $table->string('aspect_ratio')->default('1:1');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Ephemeral Stories Table
        if (!Schema::hasTable('stories')) {
            Schema::create('stories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('media_path');
                $table->string('media_type')->default('image');
                $table->unsignedSmallInteger('duration_seconds')->default(5);
                $table->string('caption')->nullable();
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }

        // 4. Social Comments Table
        if (!Schema::hasTable('social_comments')) {
            Schema::create('social_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('social_post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('comment');
                $table->boolean('is_vet_verified')->default(false);
                $table->unsignedInteger('likes_count')->default(0);
                $table->timestamps();
            });
        }

        // 5. Social Likes Table
        if (!Schema::hasTable('social_likes')) {
            Schema::create('social_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('social_post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['social_post_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_likes');
        Schema::dropIfExists('social_comments');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('social_post_media');
        Schema::dropIfExists('social_posts');
    }
};
