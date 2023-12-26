<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_meetings', function (Blueprint $table) {
           $table->unsignedBigInteger('user_id');
           $table->foreign('user_id')->references('id')->on('users');

           $table->string('meeting_id');
           $table->foreign('meeting_id')->references('meeting_id')->on('meetings');

           $table->timestamp('log_time')->default(now());
           $table->text('log_description');

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
        Schema::dropIfExists('log_meeting');
    }
};
