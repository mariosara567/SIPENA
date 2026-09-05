<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        "exam_id",
        "student_id",
        "started_at",
        "finished_at",
        "violation_count",
        "is_locked",
        "locked_reason",
        "locked_at",
        "score",
        "sync_status",
    ];

    protected function casts(): array
    {
        return [
            "started_at" => "datetime",
            "finished_at" => "datetime",
            "is_locked" => "boolean",
            "locked_at" => "datetime",
            "score" => "decimal:2",
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, "participant_id");
    }
}
