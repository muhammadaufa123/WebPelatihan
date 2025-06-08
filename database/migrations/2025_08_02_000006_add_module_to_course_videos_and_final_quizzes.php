<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            $table->foreignId('course_module_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0)->after('course_module_id');
        });

        Schema::table('final_quizzes', function (Blueprint $table) {
            $table->foreignId('course_module_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0)->after('course_module_id');
        });
    }

    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_module_id');
            $table->dropColumn('order');
        });

        Schema::table('final_quizzes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_module_id');
            $table->dropColumn('order');
        });
    }
};
