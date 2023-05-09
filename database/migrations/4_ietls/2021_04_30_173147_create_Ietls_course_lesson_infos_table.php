<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIetlsCourseLessonInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Ietls_course_lesson_infos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->id();
            $table->unsignedBigInteger('Ietls_course_lesson_id');
            $table->boolean('isLocked')->nullable()->default(1);
            $table->boolean('isContinueable')->nullable()->default(1);
            $table->boolean('isAchievable')->nullable()->default(0);
            $table->boolean('isPublished')->nullable()->default(0);
            $table->double('points')->nullable()->default(0);
            
            $table->foreign('Ietls_course_lesson_id')
            ->references('id')
            ->on('Ietls_course_lessons')
            ->onUpdate('cascade')
            ->onDelete('cascade');

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
        Schema::dropIfExists('Ietls_course_lesson_infos');
    }
}
