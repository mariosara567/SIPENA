<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Support\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParticipantManagementController extends Controller
{
    public function create(Request $request, Exam $exam): View
    {
        $this->ensureCanManageExam($request, $exam);

        $selectedClassId = $request->query('class_id');
        $studentsQuery = Student::query()->with(['user', 'schoolClass'])->orderBy('nis');

        if ($selectedClassId) {
            $studentsQuery->where('class_id', $selectedClassId);
        }

        return view('exams.participants', [
            'exam' => $exam,
            'classes' => SchoolClass::query()->orderBy('name')->get(),
            'students' => $studentsQuery->get(),
            'selectedClassId' => $selectedClassId,
        ]);
    }

    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);

        $validated = $request->validate([
            'class_id' => ['nullable', 'exists:classes,id'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);

        $studentIds = collect($validated['student_ids'] ?? []);

        if (! empty($validated['class_id'])) {
            $classStudentIds = Student::query()
                ->where('class_id', $validated['class_id'])
                ->pluck('id');

            $studentIds = $studentIds->merge($classStudentIds);
        }

        $studentIds = $studentIds->unique()->values();

        if ($studentIds->isEmpty()) {
            return back()->withErrors(['student_ids' => 'Pilih siswa atau kelas terlebih dahulu.'])->withInput();
        }

        foreach ($studentIds as $studentId) {
            $exam->participants()->firstOrCreate([
                'student_id' => $studentId,
            ], [
                'sync_status' => 'pending',
            ]);
        }

        AuditLogger::log($request->user(), 'participant.bulk_added', 'exam_participants', $exam->id, [
            'exam_id' => $exam->id,
            'count' => $studentIds->count(),
        ]);

        return redirect()
            ->route('exams.show', $exam)
            ->with('status', 'Peserta ujian berhasil ditambahkan.');
    }

    private function ensureCanManageExam(Request $request, Exam $exam): void
    {
        $user = $request->user();

        if ($user->isAdministrator()) {
            return;
        }

        if (! $user->isTeacher() || $exam->teacher_id !== $user->teacher?->id) {
            abort(403, 'Anda tidak memiliki akses ke ujian ini.');
        }
    }
}
