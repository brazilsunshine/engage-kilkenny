<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWindowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('windows', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('boarded_up')->nullable();
            $table->unsignedInteger('broken')->nullable();
            $table->unsignedInteger('double_sash')->nullable();
            $table->unsignedInteger('fan_light_decorative')->nullable();
            $table->unsignedInteger('fan_light_plain')->nullable();
            $table->unsignedInteger('stone_head')->nullable();
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
        Schema::dropIfExists('windows');
    }
}
