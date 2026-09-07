<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn("students", "nis") && !Schema::hasColumn("students", "nisn")) {
            Schema::table("students", function (Blueprint $table) {
                $table->renameColumn("nis", "nisn");
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn("students", "nisn") && !Schema::hasColumn("students", "nis")) {
            Schema::table("students", function (Blueprint $table) {
                $table->renameColumn("nisn", "nis");
            });
        }
    }
};
