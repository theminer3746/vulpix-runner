<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRunners extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runners', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('proxy_port');
            $table->unsignedSmallInteger('adb_port')->default(5555);
            $table->unsignedSmallInteger('appium_port')->default(4723);
            $table->unsignedSmallInteger('system_port')->default(8200);
            $table->string('android_version')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('runners');
    }
}
