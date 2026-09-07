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
        $sheet->fromArray([[
            "question","option_a","option_b","option_c","option_d","option_e","correct_answer","score_weight"
        ],[
            "Berapakah hasil 2 + 2?","3","4","5","6","","B",1
        ]]);
        $sheet->getStyle("A1:H1")->getFont()->setBold(true);
        foreach (range("A","H") as $column) $sheet->getColumnDimension($column)->setAutoSize(true);

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save("php://output");
        }, "template-bank-soal-my_asssesmen.xlsx");
    }

    public function create(Request $request, Exam $exam): View
    {
        $this->ensureCanManageExam($request, $exam);
        return view("exams.questions-create", ["exam" => $exam]);
    }

    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureQuestionsEditable($exam);

        $validated = $request->validate([
            "question" => ["required","string"],
            "image" => ["nullable","image","mimes:jpg,jpeg,png,webp","max:2048"],
            "option_a" => ["required","string"],
            "option_b" => ["required","string"],
            "option_c" => ["required","string"],
            "option_d" => ["required","string"],
            "option_e" => ["nullable","string"],
            "correct_answer" => ["required",Rule::in(["A","B","C","D","E"])],
            "score_weight" => ["required","numeric","gt:0"],
        ]);

        $question = $exam->questions()->create([
            "question" => $validated["question"],
            "image_path" => $request->hasFile("image")
                ? $request->file("image")->store("questions","public")
                : null,
            "option_a" => $validated["option_a"],
            "option_b" => $validated["option_b"],
            "option_c" => $validated["option_c"],
            "option_d" => $validated["option_d"],
            "option_e" => $validated["option_e"] ?? null,
            "correct_answer" => $validated["correct_answer"],
            "score_weight" => (float)$validated["score_weight"],
        ]);

        $exam->update(["question_count" => $exam->questions()->count()]);
        AuditLogger::log($request->user(),"question.created",Question::class,$question->id,["exam_id"=>$exam->id]);

        return redirect()->route("exams.show",$exam)->with("status","Soal berhasil ditambahkan.");
    }

    public function edit(Request $request, Exam $exam, Question $question): View
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureQuestionBelongsToExam($exam, $question);
        $this->ensureQuestionsEditable($exam);

        return view("exams.questions-edit", compact("exam","question"));
    }

    public function update(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureQuestionBelongsToExam($exam, $question);
        $this->ensureQuestionsEditable($exam);

        $validated = $request->validate([
            "question" => ["required","string"],
            "image" => ["nullable","image","mimes:jpg,jpeg,png,webp","max:2048"],
            "remove_image" => ["nullable","boolean"],
            "option_a" => ["required","string"],
            "option_b" => ["required","string"],
            "option_c" => ["required","string"],
            "option_d" => ["required","string"],
            "option_e" => ["nullable","string"],
            "correct_answer" => ["required",Rule::in(["A","B","C","D","E"])],
            "score_weight" => ["required","numeric","gt:0"],
        ]);

        $data = [
            "question" => $validated["question"],
            "option_a" => $validated["option_a"],
            "option_b" => $validated["option_b"],
            "option_c" => $validated["option_c"],
            "option_d" => $validated["option_d"],
            "option_e" => $validated["option_e"] ?? null,
            "correct_answer" => $validated["correct_answer"],
            "score_weight" => (float)$validated["score_weight"],
        ];

        if ($request->boolean("remove_image") && $question->image_path) {
            Storage::disk("public")->delete($question->image_path);
            $data["image_path"] = null;
        }

        if ($request->hasFile("image")) {
            if ($question->image_path) Storage::disk("public")->delete($question->image_path);
            $data["image_path"] = $request->file("image")->store("questions","public");
        }

        $question->update($data);
        AuditLogger::log($request->user(),"question.updated",Question::class,$question->id,["exam_id"=>$exam->id]);

        return redirect()->route("exams.show",$exam)->with("status","Soal berhasil diperbarui.");
    }

    public function destroy(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureQuestionBelongsToExam($exam, $question);
        $this->ensureQuestionsEditable($exam);

        if ($question->answers()->exists()) {
            return back()->withErrors(["question"=>"Soal tidak dapat dihapus karena sudah memiliki jawaban peserta."]);
        }

        $questionId = $question->id;
        if ($question->image_path) Storage::disk("public")->delete($question->image_path);
        $question->delete();
        $exam->update(["question_count"=>$exam->questions()->count()]);

        AuditLogger::log($request->user(),"question.deleted",Question::class,$questionId,["exam_id"=>$exam->id]);

        return back()->with("status","Soal berhasil dihapus.");
    }

    public function import(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureCanManageExam($request, $exam);
        $this->ensureQuestionsEditable($exam);

        $validated = $request->validate([
            "questions_file" => ["required","file","max:5120","mimes:xlsx,xls,csv,txt"],
        ]);

        try {
            $rows = $this->readRowsFromFile($validated["questions_file"]->getRealPath(),
                strtolower((string)$validated["questions_file"]->getClientOriginalExtension()));
        } catch (Throwable) {
            $rows = [];
        }

        if (count($rows) < 2) {
            return back()->withErrors(["questions_file"=>"File soal minimal harus berisi header dan satu baris data."]);
        }

        $header = array_map([$this,"normalizeCell"],$rows[0]);
        $expected = ["question","option_a","option_b","option_c","option_d","option_e","correct_answer","score_weight"];
        if (count(array_intersect($expected,$header)) < 7) {
            return back()->withErrors(["questions_file"=>"Header Excel tidak sesuai template terbaru."]);
        }

        $created = 0;
        $errors = [];

        for ($index=1; $index<count($rows); $index++) {
            $excelRow = $index + 1;
            $record = $this->mapRowByHeader($rows[$index],$header);
            if ($this->isRowEmpty($record)) continue;

            $question = trim((string)($record["question"] ?? ""));
            $optionA = trim((string)($record["option_a"] ?? ""));
            $optionB = trim((string)($record["option_b"] ?? ""));
            $optionC = trim((string)($record["option_c"] ?? ""));
            $optionD = trim((string)($record["option_d"] ?? ""));
            $optionE = trim((string)($record["option_e"] ?? ""));
            $correct = strtoupper(trim((string)($record["correct_answer"] ?? "")));
            $weight = (float)($record["score_weight"] ?? 0);

            $rowErrors = [];
            if ($question==="") $rowErrors[]="pertanyaan wajib diisi";
            foreach (["A"=>$optionA,"B"=>$optionB,"C"=>$optionC,"D"=>$optionD] as $letter=>$value) {
                if ($value==="") $rowErrors[]="pilihan {$letter} wajib diisi";
            }
            if (!in_array($correct,["A","B","C","D","E"],true)) $rowErrors[]="kunci jawaban harus A/B/C/D/E";
            if ($correct==="E" && $optionE==="") $rowErrors[]="pilihan E wajib diisi jika kunci E";
            if ($weight<=0) $rowErrors[]="bobot harus lebih dari 0";

            if ($rowErrors) {
                $errors[] = "Baris {$excelRow}: ".implode(", ",$rowErrors).".";
                continue;
            }

            $exam->questions()->create([
                "question"=>$question,"option_a"=>$optionA,"option_b"=>$optionB,
                "option_c"=>$optionC,"option_d"=>$optionD,"option_e"=>$optionE ?: null,
                "correct_answer"=>$correct,"score_weight"=>$weight,
            ]);
            $created++;
        }

        if ($created === 0) {
            $message = "Tidak ada baris soal valid yang bisa diimpor.";
            if ($errors) $message .= " ".implode(" ",array_slice($errors,0,20));
            return back()->withErrors(["questions_file"=>$message]);
        }

        $exam->update(["question_count"=>$exam->questions()->count()]);
        AuditLogger::log($request->user(),"question.imported",Question::class,null,[
            "exam_id"=>$exam->id,"count"=>$created,"errors"=>count($errors),
            "file"=>$validated["questions_file"]->getClientOriginalName(),
        ]);

        $status = "{$created} soal berhasil diimpor.";
        if ($errors) $status .= " ".count($errors)." baris dilewati karena tidak valid: ".implode(" ",array_slice($errors,0,10));
        return redirect()->route("exams.show",$exam)->with("status",$status);
    }

    private function ensureQuestionsEditable(Exam $exam): void
    {
        if ($exam->participants()->whereNotNull("started_at")->exists()) {
            abort(422,"Soal tidak dapat diubah karena sudah ada peserta yang mulai mengerjakan ujian.");
        }
    }

    private function ensureQuestionBelongsToExam(Exam $exam, Question $question): void
    {
        abort_unless($question->exam_id === $exam->id,404);
    }

    private function ensureCanManageExam(Request $request, Exam $exam): void
    {
        $user=$request->user();
        if ($user->isAdministrator()) return;
        if (!$user->isTeacher() || $exam->teacher_id !== $user->teacher?->id) abort(403,"Anda tidak memiliki akses ke ujian ini.");
    }

    private function readRowsFromFile(string $path,string $extension): array
    {
        if (in_array($extension,["csv","txt"],true)) {
            $rows=[]; $handle=fopen($path,"rb");
            if ($handle===false) return [];
            while(($data=fgetcsv($handle))!==false) $rows[]=$data;
            fclose($handle); return $rows;
        }
        return IOFactory::load($path)->getActiveSheet()->toArray(null,true,true,false);
    }

    private function normalizeCell(mixed $value): string { return strtolower(trim((string)$value)); }

    private function mapRowByHeader(array $row,array $header): array
    {
        $mapped=[];
        foreach($header as $i=>$name) if($name!=="") $mapped[$name]=$row[$i]??null;
        return $mapped;
    }

    private function isRowEmpty(array $row): bool
    {
        foreach($row as $value) if(trim((string)$value)!=="") return false;
        return true;
    }
}
