<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamParticipant;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Student;

class ExamManagementController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Exam::query()
            ->with(["subject", "teacher.user", "classes"])
            ->withCount(["questions", "participants"])
            ->latest();

        if ($user->isTeacher()) {
            $query->where("teacher_id", $user->teacher?->id ?? 0);
        }

        $exams = $query->get();
        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('level')->orderBy('name')->get();

        return view("exams.index", [
            "exams" => $exams,
            "subjects" => $subjects,
            "classes" => $classes,
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        return view("exams.create", [
            "subjects" => Subject::query()->orderBy("name")->get(),
            "teachers" => Teacher::query()->with("user")->orderBy("id")->get(),
            "classes" => SchoolClass::query()->orderBy('level')->orderBy('name')->get(),
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
            "exam_date" => ["required", "date"],
            "academic_year" => ["nullable", "string", "max:255"],
            "exam_type" => ["nullable", "string", "max:255"],
            "duration" => ["required", "integer", Rule::in([30, 45, 60, 75, 90, 120])],
            "class_ids" => ["nullable", "array"],
            "class_ids.*" => ["integer", "exists:classes,id"],
        ]);

        $teacherId = $user->isAdministrator()
            ? (int) $validated["teacher_id"]
            : (int) ($user->teacher?->id ?? 0);

        if ($teacherId === 0) {
            return back()->withErrors(["teacher_id" => "Akun guru tidak ditemukan."])->withInput();
        }

        // Validate classes level matching
        if (!empty($validated['class_ids'])) {
            $selectedClasses = SchoolClass::whereIn('id', $validated['class_ids'])->get();
            if ($selectedClasses->count() > 0) {
                $firstLevel = $selectedClasses->first()->level;
                foreach ($selectedClasses as $cls) {
                    if ($cls->level !== $firstLevel) {
                        return back()->withErrors(['class_ids' => 'Semua kelas yang dipilih harus berada pada tingkat yang sama.'])->withInput();
                    }
                }
            }
        }

        // Auto-generate token & auto-compute status
        $examDate = Carbon::parse($validated["exam_date"]);
        $today = Carbon::today();
        if ($examDate->isSameDay($today)) {
            $status = 'active';
        } elseif ($examDate->lt($today)) {
            $status = 'closed';
        } else {
            $status = 'scheduled';
        }

        $exam = Exam::query()->create([
            "subject_id" => (int) $validated["subject_id"],
            "teacher_id" => $teacherId,
            "title" => $validated["title"],
            "exam_date" => $validated["exam_date"],
            "academic_year" => $validated["academic_year"],
            "exam_type" => $validated["exam_type"],
            "duration" => $validated["duration"],
            "token" => Exam::generateUniqueToken(),
            "status" => $status,
            "question_count" => 0,
        ]);

        if (!empty($validated['class_ids'])) {
            $exam->classes()->sync($validated['class_ids']);
            
            // Auto-enroll students from the selected classes
            $studentIds = Student::whereIn('class_id', $validated['class_ids'])->pluck('id');
            foreach ($studentIds as $studentId) {
                ExamParticipant::firstOrCreate([
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                ]);
            }
        }

        AuditLogger::log($user, "exam.created", Exam::class, $exam->id, [
            "title" => $exam->title,
            "teacher_id" => $exam->teacher_id,
        ]);

        return redirect()->route("exams.show", $exam)
            ->with("status", "Ujian berhasil dibuat. Lanjutkan dengan menambahkan soal.");
    }

    public function show(Request $request, Exam $exam): View
    {
        $this->ensureCanManageExam($request, $exam);

        $exam->load(["subject", "teacher.user", "questions", "participants.student.user", "participants.student.schoolClass", "classes"]);

        // Auto-update computed status
        $computedStatus = $exam->getComputedStatus();
        if ($exam->status !== $computedStatus) {
            $exam->update(['status' => $computedStatus]);
        }

        return view("exams.show", ["exam" => $exam]);
    }

    public function edit(Request $request, Exam $exam): View
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureExamEditable($exam);

        $user = $request->user();
        $exam->load('classes');

        return view("exams.edit", [
            "exam" => $exam,
            "subjects" => Subject::query()->orderBy("name")->get(),
            "teachers" => Teacher::query()->with("user")->orderBy("id")->get(),
            "classes" => SchoolClass::query()->orderBy('level')->orderBy('name')->get(),
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
            "exam_date" => ["required", "date"],
            "academic_year" => ["nullable", "string", "max:255"],
            "exam_type" => ["nullable", "string", "max:255"],
            "duration" => ["required", "integer", Rule::in([30, 45, 60, 75, 90, 120])],
            "class_ids" => ["nullable", "array"],
            "class_ids.*" => ["integer", "exists:classes,id"],
        ]);

        $teacherId = $user->isAdministrator()
            ? (int) $validated["teacher_id"]
            : (int) ($user->teacher?->id ?? 0);

        if ($teacherId === 0) {
            return back()->withErrors(["teacher_id" => "Akun guru tidak ditemukan."])->withInput();
        }

        // Validate classes level matching
        if (!empty($validated['class_ids'])) {
            $selectedClasses = SchoolClass::whereIn('id', $validated['class_ids'])->get();
            if ($selectedClasses->count() > 0) {
                $firstLevel = $selectedClasses->first()->level;
                foreach ($selectedClasses as $cls) {
                    if ($cls->level !== $firstLevel) {
                        return back()->withErrors(['class_ids' => 'Semua kelas yang dipilih harus berada pada tingkat yang sama.'])->withInput();
                    }
                }
            }
        }

        // Auto-compute status from exam_date
        $examDate = Carbon::parse($validated["exam_date"]);
        $today = Carbon::today();
        if ($examDate->isSameDay($today)) {
            $status = 'active';
        } elseif ($examDate->lt($today)) {
            $status = 'closed';
        } else {
            $status = 'scheduled';
        }

        $exam->update([
            "subject_id" => (int) $validated["subject_id"],
            "teacher_id" => $teacherId,
            "title" => $validated["title"],
            "exam_date" => $validated["exam_date"],
            "academic_year" => $validated["academic_year"],
            "exam_type" => $validated["exam_type"],
            "duration" => $validated["duration"],
            "status" => $status,
        ]);

        if (isset($validated['class_ids'])) {
            $exam->classes()->sync($validated['class_ids']);
            
            // Auto-enroll new students from these classes
            $studentIds = Student::whereIn('class_id', $validated['class_ids'])->pluck('id');
            foreach ($studentIds as $studentId) {
                ExamParticipant::firstOrCreate([
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                ]);
            }
        } else {
            $exam->classes()->sync([]);
        }

        AuditLogger::log($user, "exam.updated", Exam::class, $exam->id);

        return redirect()->route("exams.show", $exam)->with("status", "Ujian berhasil diperbarui.");
    }

    public function destroy(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureExamEditable($exam);

        $examId = $exam->id;
        $title = $exam->title;

        if ($exam->participants()->whereNotNull("started_at")->exists()) {
            return back()->withErrors(["exam" => "Ujian tidak dapat dihapus karena sudah ada peserta yang mulai mengerjakan."]);
        }

        $exam->delete();

        AuditLogger::log($request->user(), "exam.deleted", Exam::class, $examId, ["title" => $title]);

        return redirect()->route("exams.index")->with("status", "Ujian berhasil dihapus.");
    }

    /**
     * Return participants as JSON for modal popup.
     */
    public function participantsJson(Request $request, Exam $exam): JsonResponse
    {
        $this->ensureCanManageExam($request, $exam);
        $exam->load(['participants.student.user', 'participants.student.schoolClass']);

        $data = $exam->participants->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->student->user->name ?? '-',
            'nisn' => $p->student->nisn ?? '-',
            'class' => $p->student->schoolClass->display_name ?? '-',
            'started_at' => $p->started_at?->format('d M H:i'),
            'finished_at' => $p->finished_at?->format('d M H:i'),
            'score' => $p->score,
        ]);

        return response()->json($data);
    }

    /**
     * Remove a participant from an exam.
     */
    public function removeParticipant(Request $request, Exam $exam, ExamParticipant $participant): JsonResponse
    {
        $this->ensureCanManageExam($request, $exam);

        if ($participant->exam_id !== $exam->id) {
            abort(404);
        }

        if ($participant->started_at) {
            return response()->json(['error' => 'Peserta sudah mulai mengerjakan dan tidak dapat dihapus.'], 422);
        }

        $participant->delete();

        AuditLogger::log($request->user(), 'participant.removed', ExamParticipant::class, $participant->id, [
            'exam_id' => $exam->id,
        ]);

        return response()->json(['success' => true]);
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

        if ($user->isTeacher() && $exam->teacher_id === $user->teacher->id) {
            return;
        }

        abort(403, "Anda tidak memiliki akses ke ujian ini.");
    }
}
