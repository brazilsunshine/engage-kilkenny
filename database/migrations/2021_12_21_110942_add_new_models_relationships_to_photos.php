<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewModelsRelationshipsToPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedBigInteger('lamps_id')->nullable();
            $table->unsignedBigInteger('slips_id')->nullable();
            $table->unsignedBigInteger('street_furniture_id')->nullable();
            $table->unsignedBigInteger('use_cases_id')->nullable();

            $table->foreign('lamps_id')->references('id')->on('lamps');
            $table->foreign('slips_id')->references('id')->on('slips');
            $table->foreign('street_furniture_id')->references('id')->on('street_furniture');
            $table->foreign('use_cases_id')->references('id')->on('use_cases');
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
            $table->dropForeign(['lamps_id']);
            $table->dropForeign(['slips_id']);
            $table->dropForeign(['street_furniture_id']);
            $table->dropForeign(['use_cases_id']);

            $table->dropColumn('lamps_id');
            $table->dropColumn('slips_id');
            $table->dropColumn('street_furniture_id');
            $table->dropColumn('use_cases_id');
        });
    }
}
