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
        if (!Schema::hasTable('articles')) {
            Schema::create('articles', function (Blueprint $table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('author_id')->unsigned()->nullable();
                $table->bigInteger('category_id')->unsigned()->nullable();
                $table->bigInteger('source_id')->unsigned()->nullable();
                $table->string('article_title');
                $table->string('image_url')->nullable();
                $table->string('url');
                $table->timestamp('published_at');
                $table->timestamps();
            
                // Define foreign key constraints
                $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
                $table->foreign('source_id')->references('id')->on('sources')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
