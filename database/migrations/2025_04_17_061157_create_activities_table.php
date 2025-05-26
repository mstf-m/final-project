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
        Schema::create('activities', function (Blueprint $table) {
            $table->id('activity_id');
            $table->foreignId('creator_id')
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('max_participants')->nullable();
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories', 'category_id')
                  ->onDelete('set null');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
