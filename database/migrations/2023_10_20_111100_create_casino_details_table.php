<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCasinoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casino_details', function (Blueprint $table) {
            $table->id();
			$table->text('title');
			$table->text('description');
			$table->text('sumup');
			$table->text('games');
			$table->text('fun_facts');
			$table->string('resume_1_line');
			$table->text('resume_2_words');
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
        Schema::dropIfExists('casino_details');
    }
}
