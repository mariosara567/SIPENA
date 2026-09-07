<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\ExamParticipant;
use App\Models\Question;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentExamController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user()->student;

        abort_if(!$student, 403, "Profil siswa tidak ditemukan.");

        $participants = ExamParticipant::query()
            ->where("student_id", $student->id)
            ->with(["exam.subject", "exam.teacher.user"])
            ->withCount("answers")
            ->latest()
            ->get();

        return view("student.exams-index", [
            "participants" => $participants,
        ]);
    }

    public function start(
        Request $request,
        ExamParticipant $participant,
    ): RedirectResponse {
        $student = $request->user()->student;
        abort_if(!$student || $participant->student_id !== $student->id, 403);

        $exam = $participant->exam()->with("questions")->firstOrFail();
        $now = now();

        if (!$exam->isInsideSchedule($now)) {
            return back()->withErrors([
                "token" => "Ujian belum dimulai atau sudah berakhir.",
            ]);
        }

        $validated = $request->validate([
            "token" => ["required", "string"],
        ]);

        if (
            strtoupper(trim($validated["token"])) !== strtoupper($exam->token)
        ) {
            return back()->withErrors([
                "token" => "Token ujian tidak valid.",
            ]);
        }

        if ($participant->finished_at) {
            return redirect()
                ->route("student.exams.index")
                ->with("status", "Ujian ini sudah selesai.");
        }

        if ($participant->is_locked) {
            return redirect()
                ->route("student.exams.index")
                ->withErrors([
                    "token" =>
                        "Akses ujian dikunci karena pelanggaran. Hubungi guru/pengawas untuk membuka kunci.",
                ]);
        }

        if (!$participant->started_at) {
            $participant->update([
                "started_at" => $now,
            ]);
            AuditLogger::log(
                $request->user(),
                "exam.started",
                ExamParticipant::class,
                $participant->id,
                [
                    "exam_id" => $participant->exam_id,
                ],
            );
        }

        foreach ($exam->questions as $question) {
            $participant->answers()->firstOrCreate(
                [
                    "question_id" => $question->id,
                ],
                [
                    "answer" => null,
                    "is_correct" => false,
                ],
            );
        }

        return redirect()->route("student.exams.take", $participant);
    }

    public function take(
        Request $request,
        ExamParticipant $participant,
    ): View|RedirectResponse {
        $student = $request->user()->student;
        abort_if(!$student || $participant->student_id !== $student->id, 403);

        $participant->load(["exam.questions", "answers"]);
        $exam = $participant->exam;
        $now = now();

        if (!$participant->started_at) {
            return redirect()
                ->route("student.exams.index")
                ->withErrors([
                    "token" =>
                        "Masukkan token terlebih dahulu untuk memulai ujian.",
                ]);
        }

        if ($participant->finished_at) {
            return redirect()
                ->route("student.exams.index")
                ->with("status", "Ujian sudah selesai.");
        }

        if ($participant->is_locked) {
            return redirect()
                ->route("student.exams.index")
                ->withErrors([
                    "token" =>
                        "Sesi ujian terkunci. Guru/pengawas harus membuka kunci sebelum Anda dapat melanjutkan.",
                ]);
        }

        if (!$exam->isInsideSchedule($now)) {
            $this->finalizeExam($participant, $now);

            return redirect()
                ->route("student.exams.index")
                ->with("status", "Ujian ditutup otomatis sesuai jadwal.");
        }

        $endTime = $participant->started_at
            ->copy()
            ->addMinutes($exam->duration);

        if ($now->greaterThanOrEqualTo($endTime)) {
            $this->finalizeExam($participant, $now);

            return redirect()
                ->route("student.exams.index")
                ->with("status", "Waktu habis, jawaban disubmit otomatis.");
        }

        return view("student.exam-take", [
            "participant" => $participant,
            "exam" => $exam,
            "answers" => $participant->answers->keyBy("question_id"),
            "endTimeIso" => $endTime->toIso8601String(),
        ]);
    }

    public function saveAnswer(
        Request $request,
        ExamParticipant $participant,
    ): JsonResponse {
        $student = $request->user()->student;
        abort_if(!$student || $participant->student_id !== $student->id, 403);

        $participant->load("exam");
        abort_if($participant->finished_at, 422, "Ujian sudah selesai.");
        abort_if(
            $participant->is_locked,
            423,
            "Sesi ujian dikunci oleh sistem.",
        );

        $endTime = $participant->started_at
            ?->copy()
            ->addMinutes($participant->exam->duration);
        if (
            !$participant->started_at ||
            !$endTime ||
            now()->greaterThanOrEqualTo($endTime)
        ) {
            abort(422, "Waktu ujian telah habis.");
        }

        $validated = $request->validate([
            "question_id" => [
                "required",
                "integer",
                Rule::exists("questions", "id")->where(
                    "exam_id",
                    $participant->exam_id,
                ),
            ],
            "answer" => ["nullable", Rule::in(["A", "B", "C", "D", "E"])],
        ]);

        /** @var Question $question */
        $question = Question::query()
            ->where("exam_id", $participant->exam_id)
            ->findOrFail($validated["question_id"]);
        $chosenAnswer = $validated["answer"] ?? null;

        Answer::query()->updateOrCreate(
            [
                "participant_id" => $participant->id,
                "question_id" => $question->id,
            ],
            [
                "answer" => $chosenAnswer,
                "is_correct" =>
                    $chosenAnswer !== null &&
                    $chosenAnswer === $question->correct_answer,
            ],
        );

        return response()->json([
            "saved" => true,
        ]);
    }

    public function reportViolation(
        Request $request,
        ExamParticipant $participant,
    ): JsonResponse {
        $student = $request->user()->student;
        abort_if(!$student || $participant->student_id !== $student->id, 403);
        abort_if($participant->finished_at, 422, "Ujian sudah selesai.");

        $validated = $request->validate([
            "reason" => ["nullable", "string", "max:150"],
        ]);

        $participant->increment("violation_count");
        $participant->update([
            "is_locked" => true,
            "locked_reason" =>
                $validated["reason"] ??
                "Meninggalkan halaman ujian / membuka tab lain",
            "locked_at" => now(),
        ]);

        AuditLogger::log(
            $request->user(),
            "exam.violation_detected",
            ExamParticipant::class,
            $participant->id,
            [
                "exam_id" => $participant->exam_id,
                "reason" => $participant->locked_reason,
            ],
        );

        return response()->json([
            "locked" => true,
            "violation_count" => $participant->violation_count,
        ]);
    }

    public function submit(
        Request $request,
        ExamParticipant $participant,
    ): RedirectResponse {
        $student = $request->user()->student;
        abort_if(!$student || $participant->student_id !== $student->id, 403);

        if (!$participant->finished_at) {
            $this->finalizeExam($participant, now());
            AuditLogger::log(
                $request->user(),
                "exam.submitted",
                ExamParticipant::class,
                $participant->id,
                [
                    "exam_id" => $participant->exam_id,
                    "score" => $participant->fresh()->score,
                ],
            );
        }

        return redirect()
            ->route("student.exams.index")
            ->with("status", "Jawaban berhasil dikirim.");
    }

    private function finalizeExam(
        ExamParticipant $participant,
        Carbon $finishedAt,
    ): void {
        $participant->loadMissing(["exam.questions", "answers"]);

        $questions = $participant->exam->questions;
        $answers = $participant->answers->keyBy("question_id");

        $totalWeight = (float) $questions->sum("score_weight");
        $correctWeight = 0.0;

        foreach ($questions as $question) {
            $answer = $answers->get($question->id);
            if ($answer && $answer->answer === $question->correct_answer) {
                $correctWeight += (float) $question->score_weight;
            }
        }

        $score =
            $totalWeight > 0
                ? round(($correctWeight / $totalWeight) * 100, 2)
                : 0.0;

        $participant->update([
            "finished_at" => $finishedAt,
            "score" => $score,
        ]);
    }
}
