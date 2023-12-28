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
        Schema::create('meetings', function (Blueprint $table) {
            $table->string('meeting_id')->unique();
            $table->string('meeting_name');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('description')->nullable();
            $table->text('member_list')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('all_days');
            $table->boolean('allow_freely_join');
            $table->boolean('enable_member_list_view');
            $table->boolean('enable_member_focus_view');
            $table->tinyInteger('repeat_pattern');
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
        Schema::dropIfExists('user_meetings');
    }
};
