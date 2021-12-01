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
            $table->unsignedInteger('burnt')->nullable();
            $table->unsignedInteger('clean')->nullable();
            $table->unsignedInteger('derelict')->nullable();
            $table->unsignedInteger('dirty')->nullable();
            $table->unsignedInteger('needs_powerwash')->nullable();
            $table->unsignedInteger('graffiti')->nullable();
            $table->unsignedInteger('overgrown')->nullable();
            $table->unsignedInteger('vacant')->nullable();
            $table->unsignedInteger('vandalised')->nullable();
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
