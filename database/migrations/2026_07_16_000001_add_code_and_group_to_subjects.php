<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subjects')) {
            Schema::table('subjects', function (Blueprint $table) {
                if (! Schema::hasColumn('subjects', 'code')) {
                    $table->string('code')->nullable()->unique()->after('name');
                }
                if (! Schema::hasColumn('subjects', 'group')) {
                    $table->string('group')->nullable()->after('code');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subjects')) {
            Schema::table('subjects', function (Blueprint $table) {
                if (Schema::hasColumn('subjects', 'group')) {
                    $table->dropColumn('group');
                }
                if (Schema::hasColumn('subjects', 'code')) {
                    $table->dropColumn('code');
                }
            });
        }
    }
};
