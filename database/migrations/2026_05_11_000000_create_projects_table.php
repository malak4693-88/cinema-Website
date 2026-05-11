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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('movie_name');
            $table->string('genre');
            $table->integer('duration');
            $table->date('release_date');
            $table->string('release_place');
            $table->string('language');
            $table->string('director');
            $table->string('age_rating');
            $table->decimal('ticket_price', 8, 2);
            $table->integer('available_seats');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
