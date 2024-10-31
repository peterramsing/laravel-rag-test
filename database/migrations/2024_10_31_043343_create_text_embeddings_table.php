<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextEmbeddingsTable extends Migration
{
    public function up()
    {
        Schema::create('text_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_text_id')->constrained('source_texts')->onDelete('cascade');
            $table->addColumn('vector', 'embedding', ['dimensions' => 1536])->nullable();
            $table->text('excerpt');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('text_embeddings');
    }
}
