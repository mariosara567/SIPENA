<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('exam_participants', 'sync_status')) {
            Schema::table('exam_participants', function (Blueprint $table) {
                $table->dropIndex('exam_participants_sync_status_index');
                $table->dropColumn('sync_status');
            });
        }

        Schema::dropIfExists('sync_logs');
    }

    public function down(): void
    {
        if (! Schema::hasColumn('exam_participants', 'sync_status')) {
            Schema::table('exam_participants', function (Blueprint $table) {
                $table->string('sync_status')->default('pending')->index();
            });
        }

        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('sync_date');
            $table->string('sync_type')->default('exam_results')->index();
            $table->unsignedInteger('success_records')->default(0);
            $table->unsignedInteger('failed_records')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }
};
