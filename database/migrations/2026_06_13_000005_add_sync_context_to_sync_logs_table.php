<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sync_logs', function (Blueprint $table) {
            $table->unsignedInteger('success_records')->default(0)->after('total_records');
            $table->unsignedInteger('failed_records')->default(0)->after('success_records');
            $table->string('sync_type')->default('exam_results')->index()->after('sync_date');
        });
    }

    public function down(): void
    {
        Schema::table('sync_logs', function (Blueprint $table) {
            $table->dropColumn(['success_records', 'failed_records', 'sync_type']);
        });
    }
};
