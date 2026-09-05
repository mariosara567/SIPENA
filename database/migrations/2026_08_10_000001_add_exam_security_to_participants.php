<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table("exam_participants", function (Blueprint $table) {
            $table
                ->unsignedInteger("violation_count")
                ->default(0)
                ->after("finished_at");
            $table
                ->boolean("is_locked")
                ->default(false)
                ->after("violation_count")
                ->index();
            $table->string("locked_reason")->nullable()->after("is_locked");
            $table->dateTime("locked_at")->nullable()->after("locked_reason");
        });
    }

    public function down(): void
    {
        Schema::table("exam_participants", function (Blueprint $table) {
            $table->dropIndex(["is_locked"]);
            $table->dropColumn([
                "violation_count",
                "is_locked",
                "locked_reason",
                "locked_at",
            ]);
        });
    }
};
