<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('decorative_fixing_bracket')->nullable();
            $table->unsignedInteger('ornate_hopper')->nullable();
            $table->unsignedInteger('lead')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pipes');
    }
}
