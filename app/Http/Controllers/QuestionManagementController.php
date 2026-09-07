<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Support\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class QuestionManagementController extends Controller
{
    public function template(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [
                "question",
                "option_a",
                "option_b",
                "option_c",
                "option_d",
                "option_e",
                "correct_answer",
                "score_weight",
            ],
            ["Berapakah hasil 2 + 2?", "3", "4", "5", "6", "", "B", 1],
        ]);
        $sheet->getStyle("A1:H1")->getFont()->setBold(true);
        foreach (range("A", "H") as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save("php://output");
        }, "template-bank-soal-my_asssesmen.xlsx");
    }

    public function create(Request $request, Exam $exam): View
    {
        $this->ensureCanManageExam($request, $exam);

        return view("exams.questions-create", [
            "exam" => $exam,
        ]);
    }

    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);

        $validated = $request->validate([
            "question" => ["required", "string"],
            "image" => ["nullable", "image", "mimes:jpg,jpeg,png,webp", "max:2048"],
            "option_a" => ["required", "string"],
            "option_b" => ["required", "string"],
            "option_c" => ["required", "string"],
            "option_d" => ["required", "string"],
            "option_e" => ["nullable", "string"],
            "correct_answer" => [
                "required",
                Rule::in(["A", "B", "C", "D", "E"]),
            ],
            "score_weight" => ["required", "numeric", "gt:0"],
        ]);

        $question = $exam->questions()->create([
            "question" => $validated["question"],
            "image_path" => $request->hasFile("image") ? $request->file("image")->store("questions", "public") : null,
            "option_a" => $validated["option_a"],
            "option_b" => $validated["option_b"],
            "option_c" => $validated["option_c"],
            "option_d" => $validated["option_d"],
            "option_e" => $validated["option_e"] ?? null,
            "correct_answer" => $validated["correct_answer"],
            "score_weight" => (float) $validated["score_weight"],
        ]);

        $exam->update([
            "question_count" => $exam->questions()->count(),
        ]);

        AuditLogger::log(
            $request->user(),
            "question.created",
            Question::class,
            $question->id,
            [
                "exam_id" => $exam->id,
            ],
        );

        return redirect()
            ->route("exams.show", $exam)
            ->with("status", "Soal berhasil ditambahkan.");
    }

    public function import(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);

        $validated = $request->validate([
            "questions_file" => [
                "required",
                "file",
                "max:5120",
                "mimes:xlsx,xls,csv,txt",
            ],
        ]);

        $file = $validated["questions_file"];
        $rows = $this->readRowsFromFile(
            $file->getRealPath(),
            strtolower((string) $file->getClientOriginalExtension()),
        );

        if (count($rows) < 2) {
            return back()->withErrors([
                "questions_file" =>
                    "File soal minimal harus berisi header dan satu baris data.",
            ]);
        }

        $header = array_map([$this, "normalizeCell"], $rows[0]);
        $expectedHeaders = [
            "question",
            "option_a",
            "option_b",
            "option_c",
            "option_d",
            "option_e",
            "correct_answer",
            "score_weight",
        ];
        $hasHeader = count(array_intersect($expectedHeaders, $header)) >= 7;

        $created = 0;
        $startRow = $hasHeader ? 1 : 0;

        for ($index = $startRow; $index < count($rows); $index++) {
            $raw = $rows[$index];

            $record = $hasHeader
                ? $this->mapRowByHeader($raw, $header)
                : $this->mapRowByPosition($raw);

            if ($this->isRowEmpty($record)) {
                continue;
            }

            $question = trim((string) ($record["question"] ?? ""));
            $optionA = trim((string) ($record["option_a"] ?? ""));
            $optionB = trim((string) ($record["option_b"] ?? ""));
            $optionC = trim((string) ($record["option_c"] ?? ""));
            $optionD = trim((string) ($record["option_d"] ?? ""));
            $optionE = trim((string) ($record["option_e"] ?? ""));
            $correctAnswer = strtoupper(
                trim((string) ($record["correct_answer"] ?? "")),
            );
            $scoreWeight = (float) ($record["score_weight"] ?? 0);

            if (
                $question === "" ||
                $optionA === "" ||
                $optionB === "" ||
                $optionC === "" ||
                $optionD === "" ||
                !in_array($correctAnswer, ["A", "B", "C", "D", "E"], true) ||
                $scoreWeight <= 0
            ) {
                continue;
            }

            $exam->questions()->create([
                "question" => $question,
                "option_a" => $optionA,
                "option_b" => $optionB,
                "option_c" => $optionC,
                "option_d" => $optionD,
                "option_e" => $optionE === "" ? null : $optionE,
                "correct_answer" => $correctAnswer,
                "score_weight" => $scoreWeight,
            ]);

            $created++;
        }

        if ($created === 0) {
            return back()->withErrors([
                "questions_file" =>
                    "Tidak ada baris soal valid yang bisa diimpor.",
            ]);
        }

        $exam->update([
            "question_count" => $exam->questions()->count(),
        ]);

        AuditLogger::log(
            $request->user(),
            "question.imported",
            Question::class,
            null,
            [
                "exam_id" => $exam->id,
                "count" => $created,
                "file" => $file->getClientOriginalName(),
            ],
        );

        return redirect()
            ->route("exams.show", $exam)
            ->with("status", $created . " soal berhasil diimpor dari Excel.");
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

    private function readRowsFromFile(string $path, string $extension): array
    {
        if ($extension === "csv" || $extension === "txt") {
            $rows = [];
            if (($handle = fopen($path, "rb")) === false) {
                return [];
            }

            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);

            return $rows;
        }

        try {
            $spreadsheet = IOFactory::load($path);
            return $spreadsheet
                ->getActiveSheet()
                ->toArray(null, true, true, false);
        } catch (Throwable) {
            return [];
        }
    }

    private function normalizeCell(mixed $value): string
    {
        return strtolower(trim((string) $value));
    }

    private function mapRowByHeader(array $row, array $header): array
    {
        $mapped = [];
        foreach ($header as $index => $columnName) {
            if ($columnName === "") {
                continue;
            }
            $mapped[$columnName] = $row[$index] ?? null;
        }

        return $mapped;
    }

    private function mapRowByPosition(array $row): array
    {
        return [
            "question" => $row[0] ?? null,
            "option_a" => $row[1] ?? null,
            "option_b" => $row[2] ?? null,
            "option_c" => $row[3] ?? null,
            "option_d" => $row[4] ?? null,
            "option_e" => $row[5] ?? null,
            "correct_answer" => $row[6] ?? null,
            "score_weight" => $row[7] ?? null,
        ];
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== "") {
                return false;
            }
        }

        return true;
    }
}
