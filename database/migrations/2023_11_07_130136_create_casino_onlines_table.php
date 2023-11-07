<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCasinoOnlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casino_onlines', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('nom_casino')->nullable();
            $table->string('nom_casino_slug')->nullable();
            $table->string('sous_titre')->nullable();
            $table->string('key_feature')->nullable();
            $table->string('screenshot')->nullable();
            $table->string('logo')->nullable();
            $table->string('point_pour')->nullable();
            $table->string('point_contre')->nullable();
            $table->string('bonus')->nullable();
            $table->text('sumup_description')->nullable();
            $table->text('bonus_description')->nullable();
            $table->text('deposit_mehods_description');
            $table->text('contact_information_description')->nullable();
            $table->string('contact_information')->nullable();
            $table->string('register_link');
            $table->text('description')->nullable();
            $table->string('icone')->nullable();
            $table->string('actif')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('casino_onlines');
    }
}
