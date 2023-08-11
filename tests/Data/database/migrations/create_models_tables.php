<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->string('slug_custom')->nullable();
            $table->string('author')->nullable();
            $table->string('isbn')->nullable();
            $table->text('description_custom')->nullable();
            $table->integer('year')->nullable();
            $table->string('language_slug')->nullable();
            $table->string('body_custom')->nullable();
            $table->string('time_to_read_custom')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamps();
        });
    }
};
