<?php

namespace App\Http\Controllers;

use App\Models\ExamParticipant;
use App\Models\SchoolClass;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $classId = $request->integer('class_id');
        $subjectId = $request->integer('subject_id');

        $rows = $this->buildQuery($request, $classId, $subjectId)->get();

        return view('reports.index', [
            'rows' => $rows,
            'classes' => SchoolClass::query()->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'classId' => $classId,
            'subjectId' => $subjectId,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $classId = $request->integer('class_id');
        $subjectId = $request->integer('subject_id');

        $rows = $this->buildQuery($request, $classId, $subjectId)->get();

        $filename = 'laporan-nilai-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, ['Nama Siswa', 'NIS', 'Kelas', 'Mata Pelajaran', 'Ujian', 'Nilai', 'Mulai', 'Selesai']);

            foreach ($rows as $row) {
                fputcsv($stream, [
                    $row->student_name,
                    $row->nis,
                    $row->class_name,
                    $row->subject_name,
                    $row->exam_title,
                    $row->score,
                    $row->started_at?->format('Y-m-d H:i:s'),
                    $row->finished_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $classId = $request->integer('class_id');
        $subjectId = $request->integer('subject_id');
        $rows = $this->buildQuery($request, $classId, $subjectId)->get();

        $selectedClass = $classId > 0 ? SchoolClass::query()->find($classId)?->name : 'Semua kelas';
        $selectedSubject = $subjectId > 0 ? Subject::query()->find($subjectId)?->name : 'Semua mata pelajaran';

        $pdf = Pdf::loadView('reports.pdf', [
            'rows' => $rows,
            'selectedClass' => $selectedClass ?? 'Semua kelas',
            'selectedSubject' => $selectedSubject ?? 'Semua mata pelajaran',
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-nilai-'.now()->format('Ymd-His').'.pdf');
    }

    private function buildQuery(Request $request, int $classId = 0, int $subjectId = 0)
    {
        $query = ExamParticipant::query()
            ->selectRaw("
                exam_participants.id,
                exam_participants.score,
                exam_participants.started_at,
                exam_participants.finished_at,
                users.name as student_name,
                students.nis,
                CONCAT(classes.name, COALESCE(CONCAT(' (', classes.year, ')'), '')) as class_name,
                subjects.name as subject_name,
                exams.title as exam_title
            ")
            ->join('students', 'students.id', '=', 'exam_participants.student_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->join('classes', 'classes.id', '=', 'students.class_id')
            ->join('exams', 'exams.id', '=', 'exam_participants.exam_id')
            ->join('subjects', 'subjects.id', '=', 'exams.subject_id')
            ->whereNotNull('exam_participants.finished_at')
            ->orderByDesc('exam_participants.finished_at');

        if ($classId > 0) {
            $query->where('classes.id', $classId);
        }

        if ($subjectId > 0) {
            $query->where('subjects.id', $subjectId);
        }

        if ($request->user()->isTeacher()) {
            $query->where('exams.teacher_id', $request->user()->teacher?->id ?? 0);
        }

        return $query;
    }
}
