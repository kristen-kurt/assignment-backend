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
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id(); // Primary key
                $table->unsignedBigInteger('source_id')->nullable(); // Foreign key to sources table
                $table->foreign('source_id')
                    ->references('id')
                    ->on('sources')
                    ->onDelete('cascade'); // Foreign key constraint for source_id
                $table->string('name')->unique(); // Category name (unique)
                $table->timestamps(); // Created at and Updated at timestamps
            });
         }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
