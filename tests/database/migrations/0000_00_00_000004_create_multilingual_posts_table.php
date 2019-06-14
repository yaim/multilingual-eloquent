<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultilingualPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multilingual_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('multilingual_post_id');
            $table->string('translation_language_code');
            $table->string('title');
            $table->text('content');
            $table->timestamps();

            $table->foreign('multilingual_post_id')
                  ->references('id')->on('posts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('multilingual_posts');
    }
}
