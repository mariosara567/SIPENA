<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'title',
        'exam_date',
        'academic_year',
        'exam_type',
        'duration',
        'token',
        'status',
        'question_count',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'duration' => 'integer',
        ];
    }

    /**
     * Compute status automatically based on exam_date.
     * - past date → closed
     * - today → active
     * - future date → scheduled
     */
    public function getComputedStatus(): string
    {
        if (!$this->exam_date) {
            return 'scheduled';
        }

        $today = Carbon::today();

        if ($this->exam_date->isSameDay($today)) {
            return 'active';
        }

        if ($this->exam_date->lt($today)) {
            return 'closed';
        }

        return 'scheduled';
    }

    public function getStartTimeAttribute()
    {
        return $this->exam_date ? $this->exam_date->copy()->startOfDay() : null;
    }

    public function getEndTimeAttribute()
    {
        return $this->exam_date ? $this->exam_date->copy()->endOfDay() : null;
    }

    /**
     * Generate a unique 5-character uppercase token.
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = strtoupper(Str::random(5));
        } while (self::where('token', $token)->exists());

        return $token;
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ExamParticipant::class);
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'exam_classes', 'exam_id', 'class_id');
    }

    public function isInsideSchedule(Carbon $time): bool
    {
        if (!$this->exam_date) {
            return false;
        }
        return $time->isSameDay($this->exam_date);
    }
}
