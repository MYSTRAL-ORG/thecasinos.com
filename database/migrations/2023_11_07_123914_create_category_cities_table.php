<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoryCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('city_title')->nullable();
            $table->string('city_name')->nullable();
            $table->text('header_text')->nullable();
            $table->text('footer_text')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('category_cities');
    }
}
