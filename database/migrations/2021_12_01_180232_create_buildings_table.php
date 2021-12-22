<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('derelict')->nullable();
            $table->unsignedInteger('decorative_plaster')->nullable();
            $table->unsignedInteger('mosaic')->nullable();
            $table->unsignedInteger('plaque')->nullable();
            $table->unsignedInteger('poorly_maintained')->nullable();
            $table->unsignedInteger('projecting_chimney')->nullable();
            $table->unsignedInteger('stone_arch')->nullable();
            $table->unsignedInteger('stone_step')->nullable();
            $table->unsignedInteger('vacant')->nullable();
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
        Schema::dropIfExists('buildings');
    }
}
