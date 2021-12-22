<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoofsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('overhanging')->nullable();
            $table->unsignedInteger('bracketed_eaves')->nullable();
            $table->unsignedInteger('small_slates')->nullable();
            $table->unsignedInteger('large_slates')->nullable();
            $table->unsignedInteger('lead_ridges')->nullable();
            $table->unsignedInteger('degrated')->nullable();
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
        Schema::dropIfExists('roofs');
    }
}
