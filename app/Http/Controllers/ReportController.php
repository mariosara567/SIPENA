<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamParticipant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        
        $examQuery = Exam::query()
            ->with(["subject", "teacher.user", "classes"])
            ->latest();
            
        if ($user->isTeacher()) {
            $examQuery->where("teacher_id", $user->teacher?->id ?? 0);
        }

        $exams = $examQuery->get();

        return view('reports.index', [
            'exams' => $exams,
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

        // All exams for the horizontal slider
        $examQuery = Exam::query()
            ->with(["subject", "classes"])
            ->latest();
        if ($user->isTeacher()) {
            $examQuery->where("teacher_id", $user->teacher?->id ?? 0);
        }
        $allExams = $examQuery->get();

        // Get participants for this exam who have finished
        $participants = ExamParticipant::query()
            ->where('exam_id', $exam->id)
            ->whereNotNull('finished_at')
            ->with(['student.user'])
            ->orderBy('score', 'desc')
            ->get();

        $total = $participants->count();

        // Calculate stats
        $stats = [
            'sangat_baik' => ['count' => 0, 'percent' => 0],
            'baik' => ['count' => 0, 'percent' => 0],
            'cukup' => ['count' => 0, 'percent' => 0],
            'kurang' => ['count' => 0, 'percent' => 0],
        ];

        foreach ($participants as $p) {
            $score = $p->score;
            if ($score >= 93) {
                $stats['sangat_baik']['count']++;
            } elseif ($score >= 85) {
                $stats['baik']['count']++;
            } elseif ($score >= 76) {
                $stats['cukup']['count']++;
            } else {
                $stats['kurang']['count']++;
            }
        }

        if ($total > 0) {
            foreach ($stats as $key => $data) {
                $stats[$key]['percent'] = round(($data['count'] / $total) * 100, 2);
            }
        }

        return view('reports.show', compact('exam', 'allExams', 'participants', 'stats', 'total'));
    }

    public function exportExcel(Request $request, Exam $exam): StreamedResponse
    {
        $user = $request->user();
        abort_unless(
            $user->isAdministrator() || ($user->isTeacher() && $exam->teacher_id === $user->teacher?->id),
            403
        );

        $exam->load(['subject', 'classes']);
        
        $participants = ExamParticipant::query()
            ->where('exam_id', $exam->id)
            ->whereNotNull('finished_at')
            ->with(['student.user'])
            ->orderBy('score', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Nilai Ujian');

        // Styles
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'] // Primary color
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $borderStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
        ];

        // Title
        $sheet->setCellValue('A1', 'DAFTAR NILAI UJIAN');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'Mata Pelajaran:');
        $sheet->setCellValue('B3', $exam->subject->name);
        $sheet->setCellValue('A4', 'Ujian:');
        $sheet->setCellValue('B4', $exam->title);
        $sheet->setCellValue('A5', 'Kelas:');
        $sheet->setCellValue('B5', $exam->classes->pluck('display_name')->join(', '));
        
        // Headers
        $headers = ['NO', 'NAMA MURID', 'NISN', 'KELAS', 'NILAI UJIAN'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '7', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $sheet->getStyle('A7:E7')->applyFromArray($headerStyle);

        // Data
        $row = 8;
        $no = 1;
        foreach ($participants as $participant) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $participant->student->user->name);
            $sheet->setCellValue('C' . $row, $participant->student->nisn . ' '); // Add space so excel treats as string
            $sheet->setCellValue('D' . $row, $participant->student->schoolClass?->display_name);
            $sheet->setCellValue('E' . $row, $participant->score);
            
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($borderStyle);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
            $no++;
        }

        $filename = 'Nilai_Ujian_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $exam->title) . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
