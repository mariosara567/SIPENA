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
        Schema::table('exams', function (Blueprint $table) {
            $table->date('exam_date')->nullable()->after('title');
            $table->string('academic_year')->nullable()->after('exam_date');
            $table->string('exam_type')->nullable()->after('academic_year');
        });

        // Convert existing start_time to exam_date if there's any data
        DB::statement('UPDATE exams SET exam_date = DATE(start_time)');

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });

        Schema::create('exam_classes', function (Blueprint $table) {
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->primary(['exam_id', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_classes');

        Schema::table('exams', function (Blueprint $table) {
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
        });

        DB::statement('UPDATE exams SET start_time = CAST(exam_date AS TIMESTAMP), end_time = CAST(exam_date AS TIMESTAMP) + INTERVAL \'1 day\'');

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['exam_date', 'academic_year', 'exam_type']);
        });
    }
};
