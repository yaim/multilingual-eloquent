<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('author_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author_id');
            $table->string('language_code');
            $table->string('name');
            $table->text('bio');
            $table->timestamps();

            $table->foreign('author_id')
                  ->references('id')->on('authors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('author_translations');
    }
}
