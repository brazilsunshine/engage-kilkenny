<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuildingsToPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedBigInteger('buildings_id')->nullable();
            $table->unsignedBigInteger('cables_id')->nullable();
            $table->unsignedBigInteger('pipes_id')->nullable();
            $table->unsignedBigInteger('roofs_id')->nullable();
            $table->unsignedBigInteger('signs_id')->nullable();
            $table->unsignedBigInteger('walls_id')->nullable();
            $table->unsignedBigInteger('windows_id')->nullable();

            $table->foreign('buildings_id')->references('id')->on('buildings');
            $table->foreign('cables_id')->references('id')->on('cables');
            $table->foreign('pipes_id')->references('id')->on('pipes');
            $table->foreign('roofs_id')->references('id')->on('roofs');
            $table->foreign('signs_id')->references('id')->on('signs');
            $table->foreign('walls_id')->references('id')->on('walls');
            $table->foreign('windows_id')->references('id')->on('windows');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['buildings_id']);
            $table->dropForeign(['cables_id']);
            $table->dropForeign(['pipes_id']);
            $table->dropForeign(['roofs_id']);
            $table->dropForeign(['signs_id']);
            $table->dropForeign(['walls_id']);
            $table->dropForeign(['windows_id']);

            $table->dropColumn('buildings_id');
            $table->dropColumn('cables_id');
            $table->dropColumn('pipes_id');
            $table->dropColumn('roofs_id');
            $table->dropColumn('signs_id');
            $table->dropColumn('walls_id');
            $table->dropColumn('windows_id');
        });
    }
}
