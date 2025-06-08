<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('path_video');
        });

        Schema::table('final_quizzes', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('passing_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('final_quizzes', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
