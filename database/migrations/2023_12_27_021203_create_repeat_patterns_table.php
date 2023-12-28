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
        Schema::create('repeat_patterns', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_id');
            $table->foreign('meeting_id')
            ->references('meeting_id')
            ->on('meetings');
            $table->enum('repeat_type', ['none', 'daily', 'weekly', 'weekdays'])->default('none');
            $table->string('day_of_week')->nullable();
            $table->string('custom_days')->nullable();
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
        Schema::dropIfExists('repeat_patterns');
    }
};
