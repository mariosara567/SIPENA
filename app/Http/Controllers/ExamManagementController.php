<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Teacher;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExamManagementController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Exam::query()
            ->with(["subject", "teacher.user"])
            ->withCount(["questions", "participants"])
            ->latest();

        if ($user->isTeacher()) {
            $query->where("teacher_id", $user->teacher?->id ?? 0);
        }

        return view("exams.index", ["exams" => $query->get()]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        return view("exams.create", [
            "subjects" => Subject::query()->orderBy("name")->get(),
            "teachers" => Teacher::query()->with("user")->orderBy("id")->get(),
            "isAdministrator" => $user->isAdministrator(),
            "defaultTeacherId" => $user->teacher?->id,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            "subject_id" => ["required", "exists:subjects,id"],
            "teacher_id" => [Rule::requiredIf($user->isAdministrator()), "nullable", "exists:teachers,id"],
            "title" => ["required", "string", "max:255"],
            "start_time" => ["required", "date"],
            "end_time" => ["required", "date", "after:start_time"],
            "status" => ["required", Rule::in(["draft", "scheduled", "active", "closed"])],
        ]);

        $teacherId = $user->isAdministrator()
            ? (int) $validated["teacher_id"]
            : (int) ($user->teacher?->id ?? 0);

        if ($teacherId === 0) {
            return back()->withErrors(["teacher_id" => "Akun guru tidak ditemukan."])->withInput();
        }

        $start = Carbon::parse($validated["start_time"]);
        $end = Carbon::parse($validated["end_time"]);
        $duration = max(1, $start->diffInMinutes($end));

        $exam = Exam::query()->create([
            "subject_id" => (int) $validated["subject_id"],
            "teacher_id" => $teacherId,
            "title" => $validated["title"],
            "start_time" => $start,
            "end_time" => $end,
            "duration" => $duration,
            "token" => $this->generateToken(),
            "status" => $validated["status"],
            "question_count" => 0,
        ]);

        AuditLogger::log($user, "exam.created", Exam::class, $exam->id, [
            "title" => $exam->title,
            "teacher_id" => $exam->teacher_id,
        ]);

        return redirect()->route("exams.show", $exam)
            ->with("status", "Ujian berhasil dibuat. Lanjutkan dengan menambahkan soal dan peserta.");
    }

    public function show(Request $request, Exam $exam): View
    {
        $this->ensureCanManageExam($request, $exam);

        $exam->load(["subject", "teacher.user", "questions", "participants.student.user"]);

        return view("exams.show", ["exam" => $exam]);
    }

    public function edit(Request $request, Exam $exam): View
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureExamEditable($exam);

        $user = $request->user();

        return view("exams.edit", [
            "exam" => $exam,
            "subjects" => Subject::query()->orderBy("name")->get(),
            "teachers" => Teacher::query()->with("user")->orderBy("id")->get(),
            "isAdministrator" => $user->isAdministrator(),
        ]);
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureExamEditable($exam);

        $user = $request->user();

        $validated = $request->validate([
            "subject_id" => ["required", "exists:subjects,id"],
            "teacher_id" => [Rule::requiredIf($user->isAdministrator()), "nullable", "exists:teachers,id"],
            "title" => ["required", "string", "max:255"],
            "start_time" => ["required", "date"],
            "end_time" => ["required", "date", "after:start_time"],
            "status" => ["required", Rule::in(["draft", "scheduled", "active", "closed"])],
        ]);

        $teacherId = $user->isAdministrator()
            ? (int) $validated["teacher_id"]
            : (int) ($user->teacher?->id ?? 0);

        if ($teacherId === 0) {
            return back()->withErrors(["teacher_id" => "Akun guru tidak ditemukan."])->withInput();
        }

        $start = Carbon::parse($validated["start_time"]);
        $end = Carbon::parse($validated["end_time"]);

        $exam->update([
            "subject_id" => (int) $validated["subject_id"],
            "teacher_id" => $teacherId,
            "title" => $validated["title"],
            "start_time" => $start,
            "end_time" => $end,
            "duration" => max(1, $start->diffInMinutes($end)),
            "status" => $validated["status"],
        ]);

        AuditLogger::log($user, "exam.updated", Exam::class, $exam->id);

        return redirect()->route("exams.show", $exam)->with("status", "Ujian berhasil diperbarui.");
    }

    public function destroy(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureExamEditable($exam);

        $examId = $exam->id;
        $title = $exam->title;

        // Jangan menghapus ujian yang sudah memiliki peserta yang mulai mengerjakan.
        if ($exam->participants()->whereNotNull("started_at")->exists()) {
            return back()->withErrors(["exam" => "Ujian tidak dapat dihapus karena sudah ada peserta yang mulai mengerjakan."]);
        }

        $exam->delete();

        AuditLogger::log($request->user(), "exam.deleted", Exam::class, $examId, ["title" => $title]);

        return redirect()->route("exams.index")->with("status", "Ujian berhasil dihapus.");
    }

    private function generateToken(): string
    {
        do {
            $token = strtoupper(Str::random(5));
        } while (Exam::query()->where("token", $token)->exists());

        return $token;
    }

    private function ensureExamEditable(Exam $exam): void
    {
        if ($exam->participants()->whereNotNull("started_at")->exists()) {
            abort(422, "Ujian tidak dapat diubah karena sudah ada peserta yang mulai mengerjakan.");
        }
    }

    private function ensureCanManageExam(Request $request, Exam $exam): void
    {
        $user = $request->user();

        if ($user->isAdministrator()) {
            return;
        }

        if (!$user->isTeacher() || $exam->teacher_id !== $user->teacher?->id) {
            abort(403, "Anda tidak memiliki akses ke ujian ini.");
        }
    }
}
