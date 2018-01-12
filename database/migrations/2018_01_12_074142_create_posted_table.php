<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('posted', function (Blueprint $table) {
		    $table->increments('id');
		    $table->integer('user_id')->unsigned();
		    $table->string('provider');
		    $table->string('title');
		    $table->text('text');
		    $table->string('img');
		    $table->string('link');
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
	    //Schema::dropIfExists('posted');
    }
}
