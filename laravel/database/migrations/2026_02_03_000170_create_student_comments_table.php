<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('group_id')->nullable();
            $table->string('schedule_id')->nullable();
            $table->text('comment');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->nullOnDelete();
            $table->foreign('schedule_id')->references('id')->on('schedules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_comments');
    }
};
