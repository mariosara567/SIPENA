<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamParticipant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Support\AuditLogger;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $statusFilter = $request->query('status', 'all');

        $examQuery = Exam::query()
            ->with(["subject", "teacher.user", "classes"])
            ->latest();
        if ($user->isTeacher()) {
            $examQuery->where("teacher_id", $user->teacher?->id ?? 0);
        }

        // Auto-update statuses based on exam_date
        $allExams = (clone $examQuery)->get();
        foreach ($allExams as $exam) {
            $computedStatus = $exam->getComputedStatus();
            if ($exam->status !== $computedStatus && !in_array($exam->status, ['closed'])) {
                if (!($exam->status === 'closed' && $computedStatus !== 'closed')) {
                    $exam->update(['status' => $computedStatus]);
                }
            }
        }

        // Re-fetch after status update
        $examQuery2 = Exam::query()
            ->with(["subject", "teacher.user", "classes"])
            ->latest();
        if ($user->isTeacher()) {
            $examQuery2->where("teacher_id", $user->teacher?->id ?? 0);
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            $examQuery2->where('status', $statusFilter);
        }

        $examIds = (clone $examQuery2)->pluck("id");

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
            ->with(["subject", "teacher.user", "classes"])
            ->withCount("participants")
            ->withCount([
                "participants as in_progress_count" => fn($query) => $query
                    ->whereNotNull("started_at")
                    ->whereNull("finished_at"),
                "participants as completed_count" => fn(
                    $query,
                ) => $query->whereNotNull("finished_at"),
            ])
            ->latest()
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

        // Count per status for tab badges
        $allExamIds = Exam::query();
        if ($user->isTeacher()) {
            $allExamIds->where("teacher_id", $user->teacher?->id ?? 0);
        }
        $statusCounts = [
            'all' => (clone $allExamIds)->count(),
            'active' => (clone $allExamIds)->where('status', 'active')->count(),
            'scheduled' => (clone $allExamIds)->where('status', 'scheduled')->count(),
            'closed' => (clone $allExamIds)->where('status', 'closed')->count(),
        ];

        return view("monitoring.index", [
            "total" => $total,
            "online" => $online,
            "inProgress" => $inProgress,
            "completed" => $completed,
            "rows" => $rows,
            "statusFilter" => $statusFilter,
            "statusCounts" => $statusCounts,
        ]);
    }

    public function show(Request $request, Exam $exam): View
    {
        $user = $request->user();
        abort_unless(
            $user->isAdministrator() || ($user->isTeacher() && $exam->teacher_id === $user->teacher?->id),
            403
        );

        $exam->load(['subject', 'classes']);

        $participants = ExamParticipant::query()
            ->where('exam_id', $exam->id)
            ->with(['student.user'])
            ->get();

        return view('monitoring.show', compact('exam', 'participants'));
    }

    /**
     * Generate a new random token for an exam.
     */
    public function generateToken(Request $request, Exam $exam): JsonResponse
    {
        $user = $request->user();
        abort_unless(
            $user->isAdministrator() ||
                ($user->isTeacher() && $exam->teacher_id === $user->teacher?->id),
            403,
        );

        $newToken = Exam::generateUniqueToken();
        $exam->update(['token' => $newToken]);

        AuditLogger::log($user, 'exam.token_regenerated', Exam::class, $exam->id, [
            'new_token' => $newToken,
        ]);

        return response()->json(['token' => $newToken]);
    }

    /**
     * Override status for an exam (e.g. force close).
     */
    public function updateStatus(Request $request, Exam $exam): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            $user->isAdministrator() ||
                ($user->isTeacher() && $exam->teacher_id === $user->teacher?->id),
            403,
        );

        $validated = $request->validate([
            'status' => ['required', 'in:scheduled,active,closed'],
        ]);

        $exam->update(['status' => $validated['status']]);

        AuditLogger::log($user, 'exam.status_changed', Exam::class, $exam->id, [
            'new_status' => $validated['status'],
        ]);

        return back()->with('status', 'Status ujian berhasil diubah.');
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
