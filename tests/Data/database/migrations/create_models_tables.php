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
            $table->string('uuid')->nullable();
            $table->string('slug_sort')->nullable();
            $table->string('slug_custom')->nullable();
            $table->string('contributor')->nullable();
            $table->text('description_custom')->nullable();
            $table->integer('year')->nullable();
            $table->dateTime('released_on')->nullable();
            $table->string('rights')->nullable();
            $table->integer('serie_id')->nullable();
            $table->string('author')->nullable();
            $table->integer('author_id')->nullable();
            $table->integer('volume')->nullable();
            $table->integer('publisher_id')->nullable();
            $table->string('language_slug')->nullable();
            $table->integer('page_count')->nullable();
            $table->boolean('is_maturity_rating')->nullable();
            $table->boolean('is_hidden')->nullable();
            $table->string('type')->nullable();
            $table->string('isbn')->nullable();
            $table->string('isbn10')->nullable();
            $table->string('isbn13')->nullable();
            $table->json('identifiers')->nullable();
            $table->string('google_book_id')->nullable();

            $table->string('body_custom')->nullable();
            $table->string('time_to_read_custom')->nullable();

            $table->string('publish_status')->nullable();
            $table->dateTime('publish_at')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();

            $table->timestamps();
        });
    }
};
