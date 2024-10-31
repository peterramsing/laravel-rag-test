<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPgvectorExtension extends Migration
{
    public function up()
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
    }

    public function down()
    {
        DB::statement('DROP EXTENSION IF EXISTS vector');
    }
}
