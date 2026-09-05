<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamParticipant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Support\AuditLogger;

class MonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $examQuery = Exam::query()
            ->with(["subject", "teacher.user"])
            ->latest();
        if ($user->isTeacher()) {
            $examQuery->where("teacher_id", $user->teacher?->id ?? 0);
        }

        $examIds = $examQuery->pluck("id");

        $participants = ExamParticipant::query()->whereIn("exam_id", $examIds);
        $total = (clone $participants)->count();
        $inProgress = (clone $participants)
            ->whereNotNull("started_at")
            ->whereNull("finished_at")
            ->count();
        $completed = (clone $participants)
            ->whereNotNull("finished_at")
            ->count();
        $online = (clone $participants)
            ->whereNotNull("started_at")
            ->whereNull("finished_at")
            ->where("updated_at", ">=", now()->subMinutes(5))
            ->count();

        $rows = Exam::query()
            ->whereIn("id", $examIds)
            ->with(["subject", "teacher.user"])
            ->withCount("participants")
            ->withCount([
                "participants as in_progress_count" => fn($query) => $query
                    ->whereNotNull("started_at")
                    ->whereNull("finished_at"),
                "participants as completed_count" => fn(
                    $query,
                ) => $query->whereNotNull("finished_at"),
            ])
            ->get()
            ->map(function (Exam $exam) {
                $questionsCount = max($exam->questions()->count(), 1);
                $answerCount = $exam
                    ->participants()
                    ->withCount("answers")
                    ->get()
                    ->sum("answers_count");
                $maxAnswers = max(
                    $exam->participants_count * $questionsCount,
                    1,
                );
                $progress = round(($answerCount / $maxAnswers) * 100, 1);

                return [
                    "exam" => $exam,
                    "progress" => $progress,
                ];
            });

        $activeParticipants = ExamParticipant::query()
            ->whereIn("exam_id", $examIds)
            ->whereNotNull("started_at")
            ->whereNull("finished_at")
            ->with(["student.user", "student.schoolClass", "exam.subject"])
            ->latest("updated_at")
            ->get();

        return view("monitoring.index", [
            "total" => $total,
            "online" => $online,
            "inProgress" => $inProgress,
            "completed" => $completed,
            "rows" => $rows,
            "activeParticipants" => $activeParticipants,
        ]);
    }

    public function unlock(
        Request $request,
        ExamParticipant $participant,
    ): RedirectResponse {
        $participant->load("exam");
        $user = $request->user();
        abort_unless(
            $user->isAdministrator() ||
                ($user->isTeacher() &&
                    $participant->exam->teacher_id === $user->teacher?->id),
            403,
        );

        $participant->update([
            "is_locked" => false,
            "locked_reason" => null,
            "locked_at" => null,
        ]);

        AuditLogger::log(
            $user,
            "exam.participant_unlocked",
            ExamParticipant::class,
            $participant->id,
            [
                "exam_id" => $participant->exam_id,
            ],
        );

        return back()->with("status", "Kunci ujian siswa berhasil dibuka.");
    }
}
