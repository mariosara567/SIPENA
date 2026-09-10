<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('classes')) {
            Schema::table('classes', function (Blueprint $table) {
                if (Schema::hasColumn('classes', 'year')) {
                    $table->dropColumn('year');
                }
                if (! Schema::hasColumn('classes', 'level')) {
                    $table->unsignedTinyInteger('level')->default(10)->after('name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('classes')) {
            Schema::table('classes', function (Blueprint $table) {
                if (Schema::hasColumn('classes', 'level')) {
                    $table->dropColumn('level');
                }
                if (! Schema::hasColumn('classes', 'year')) {
                    $table->smallInteger('year')->nullable()->after('name');
                }
            });
        }
    }
};
