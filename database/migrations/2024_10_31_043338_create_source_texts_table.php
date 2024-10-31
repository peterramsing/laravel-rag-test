<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourceTextsTable extends Migration
{
    public function up()
    {
        Schema::create('source_texts', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('source_texts');
    }
}
