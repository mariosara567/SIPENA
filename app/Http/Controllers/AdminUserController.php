<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminUserController extends Controller
{
    public function studentTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Siswa");
        $sheet->fromArray([
            ["nama", "nisn", "kelas"],
            ["Yohanes Carlos Ngaga Sara", "0123456789", ""],
        ]);
        $sheet->getStyle("A1:C1")->getFont()->setBold(true);
        $sheet->getStyle("B:B")->getNumberFormat()->setFormatCode("@");

        $classSheet = $spreadsheet->createSheet();
        $classSheet->setTitle("Daftar Kelas");
        $classes = SchoolClass::query()->orderBy("name")->get();
        foreach ($classes as $i => $class) {
            $classSheet->setCellValue("A" . ($i + 1), $class->display_name);
        }
        $classSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        if (count($classes) > 0) {
            $spreadsheet->addNamedRange(new NamedRange("ClassList", $classSheet, "\$A\$1:\$A\$" . count($classes)));
            $validation = $sheet->getCell("C2")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle("Kelas tidak valid");
            $validation->setError("Pilih kelas dari daftar yang tersedia di aplikasi.");
            $validation->setFormula1("=ClassList");
            for ($row = 2; $row <= 500; $row++) {
                $sheet->getCell("C{$row}")->setDataValidation(clone $validation);
            }
        }

        foreach (["A" => 34, "B" => 16, "C" => 24] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save("php://output");
        }, "template-import-siswa-my_asssesmen.xlsx");
    }

    public function importStudents(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "students_file" => ["required", "file", "max:5120", "mimes:xlsx,xls,csv"],
        ]);

        try {
            $rows = IOFactory::load($validated["students_file"]->getRealPath())->getActiveSheet()->toArray();
        } catch (Throwable) {
            return back()->withErrors(["students_file" => "File tidak dapat dibaca. Gunakan template Excel My Asssesmen."]);
        }

        if (count($rows) < 2) {
            return back()->withErrors(["students_file" => "File Excel belum memiliki data siswa."]);
        }

        $header = array_map(fn($v) => strtolower(trim((string)$v)), $rows[0]);
        $expected = ["nama", "nisn", "kelas"];
        foreach ($expected as $required) {
            if (!in_array($required, $header, true)) {
                return back()->withErrors(["students_file" => "Kolom {$required} wajib ada. Gunakan template terbaru."]);
            }
        }

        $map = array_flip($header);
        $records = [];
        foreach (array_slice($rows, 1) as $index => $row) {
            $excelRow = $index + 2;
            $name = trim((string)($row[$map["nama"]] ?? ""));
            $nisn = trim((string)($row[$map["nisn"]] ?? ""));
            $className = trim((string)($row[$map["kelas"]] ?? ""));
            if ($name === "" && $nisn === "" && $className === "") continue;

            if ($name === "" || !preg_match('/^\d{10}$/', $nisn) || $className === "") {
                return back()->withErrors(["students_file" => "Baris {$excelRow}: nama, kelas wajib diisi dan NISN harus tepat 10 digit."]);
            }
            $class = SchoolClass::query()->get()->first(fn ($item) => $item->display_name === $className || $item->name === $className);
            if (!$class) return back()->withErrors(["students_file" => "Baris {$excelRow}: kelas '{$className}' tidak ditemukan di aplikasi."]);
            if (Student::query()->where("nisn", $nisn)->exists()) return back()->withErrors(["students_file" => "Baris {$excelRow}: NISN {$nisn} sudah terdaftar."]);
            $records[] = compact("name", "nisn", "class");
        }

        $created = 0;
        DB::transaction(function () use ($records, &$created): void {
            foreach ($records as $record) {
                $username = $record["nisn"];
                if (User::query()->where("username", $username)->exists()) {
                    throw new \RuntimeException("Username NISN {$username} sudah digunakan.");
                }
                $user = User::query()->create([
                    "name" => $record["name"],
                    "username" => $username,
                    "role" => "student",
                    "password" => Hash::make($username),
                ]);
                Student::query()->create([
                    "user_id" => $user->id,
                    "class_id" => $record["class"]->id,
                    "nisn" => $record["nisn"],
                ]);
                $created++;
            }
        });

        AuditLogger::log($request->user(), "student.imported", Student::class, null, ["created" => $created]);
        return back()->with("status", "{$created} siswa berhasil diimpor. Username dan password awal menggunakan NISN.");
    }

    public function teacherTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ["nama", "nip", "subject"],
            ["Yohanes Carlos", "198001012010011001", "Matematika"],
        ]);
        $sheet->getStyle("A1:C1")->getFont()->setBold(true);
        $sheet->getStyle("B:B")->getNumberFormat()->setFormatCode("@");
        foreach (["A" => 34, "B" => 20, "C" => 28] as $column => $width) $sheet->getColumnDimension($column)->setWidth($width);
        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save("php://output");
        }, "template-import-guru-my_asssesmen.xlsx");
    }

    public function importTeachers(Request $request): RedirectResponse
    {
        $validated = $request->validate(["teachers_file" => ["required", "file", "max:5120", "mimes:xlsx,xls,csv"]]);
        try {
            $rows = IOFactory::load($validated["teachers_file"]->getRealPath())->getActiveSheet()->toArray();
        } catch (Throwable) {
            return back()->withErrors(["teachers_file" => "File tidak dapat dibaca. Gunakan template Excel My Asssesmen."]);
        }
        if (count($rows) < 2) return back()->withErrors(["teachers_file" => "File Excel belum memiliki data guru."]);
        $header = array_map(fn($v) => strtolower(trim((string)$v)), $rows[0]);
        foreach (["nama", "nip", "subject"] as $required) if (!in_array($required, $header, true)) return back()->withErrors(["teachers_file" => "Kolom {$required} wajib ada. Gunakan template terbaru."]);
        $map = array_flip($header); $records=[]; $reserved=[];
        foreach (array_slice($rows,1) as $index=>$row) {
            $excelRow=$index+2; $name=trim((string)($row[$map["nama"]]??"")); $nip=trim((string)($row[$map["nip"]]??"")); $subject=trim((string)($row[$map["subject"]]??""));
            if ($name==="" && $nip==="" && $subject==="") continue;
            if ($name==="") return back()->withErrors(["teachers_file"=>"Baris {$excelRow}: nama wajib diisi."]);
            if ($nip!=="" && !preg_match('/^\d{18}$/',$nip)) return back()->withErrors(["teachers_file"=>"Baris {$excelRow}: NIP harus tepat 18 digit jika diisi."]);
            if ($nip!=="" && Teacher::query()->where("nip",$nip)->exists()) return back()->withErrors(["teachers_file"=>"Baris {$excelRow}: NIP {$nip} sudah terdaftar."]);
            $first=preg_replace('/[^a-z0-9]/','',Str::lower(Str::before(trim($name),' '))); $first=$first?:'guru';
            $username=$first; $n=1; while(User::query()->where("username",$username)->exists() || in_array($username,$reserved,true)){ $n++; $username=$first.$n; } $reserved[]=$username;
            $records[]=compact("name","nip","subject","username");
        }
        $created=0;
        DB::transaction(function() use($records,&$created):void{ foreach($records as $r){ $password=$r["nip"]!==""?$r["nip"]:$r["username"]; $user=User::query()->create(["name"=>$r["name"],"username"=>$r["username"],"role"=>"teacher","password"=>Hash::make($password)]); Teacher::query()->create(["user_id"=>$user->id,"nip"=>$r["nip"]?:null,"subject"=>$r["subject"]?:null]); $created++; }});
        AuditLogger::log($request->user(),"teacher.imported",Teacher::class,null,["created"=>$created]);
        return back()->with("status","{$created} guru berhasil diimpor. Password awal menggunakan NIP jika tersedia, jika tidak menggunakan username.");
    }

    public function teachers(): View
    {
        return view("admin.teachers", [
            "teachers" => Teacher::query()->with("user")->latest()->get(),
        ]);
    }

    public function storeTeacher(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:120"],
            "username" => [
                "required",
                "string",
                "max:50",
                "alpha_dash",
                "unique:users,username",
            ],
            "password" => ["required", "string", "min:6"],
            "nip" => ["nullable", "string", "max:50", "unique:teachers,nip"],
            "subject" => ["nullable", "string", "max:100"],
        ]);

        $user = User::query()->create([
            "name" => $validated["name"],
            "username" => $validated["username"],
            "role" => "teacher",
            "password" => Hash::make($validated["password"]),
        ]);

        $teacher = Teacher::query()->create([
            "user_id" => $user->id,
            "nip" => $validated["nip"],
            "subject" => $validated["subject"],
        ]);

        AuditLogger::log(
            $request->user(),
            "teacher.created",
            Teacher::class,
            $teacher->id,
            ["username" => $user->username],
        );

        return back()->with("status", "Akun guru berhasil dibuat.");
    }

    public function updateTeacher(
        Request $request,
        Teacher $teacher,
    ): RedirectResponse {
        $validated = $request->validate([
            "name" => ["required", "string", "max:120"],
            "username" => [
                "required",
                "string",
                "max:50",
                "alpha_dash",
                "unique:users,username," . $teacher->user_id,
            ],
            "nip" => [
                "nullable",
                "string",
                "max:50",
                "unique:teachers,nip," . $teacher->id,
            ],
            "subject" => ["nullable", "string", "max:100"],
        ]);

        $teacher->user()->update([
            "name" => $validated["name"],
            "username" => $validated["username"],
        ]);

        $teacher->update([
            "nip" => $validated["nip"],
            "subject" => $validated["subject"],
        ]);

        AuditLogger::log(
            $request->user(),
            "teacher.updated",
            Teacher::class,
            $teacher->id,
            ["username" => $validated["username"]],
        );

        return back()->with("status", "Akun guru berhasil diperbarui.");
    }

    public function resetTeacherPassword(
        Request $request,
        Teacher $teacher,
    ): RedirectResponse {
        $validated = $request->validate([
            "password" => ["required", "string", "min:6"],
        ]);

        $teacher->user()->update([
            "password" => Hash::make($validated["password"]),
        ]);

        AuditLogger::log(
            $request->user(),
            "teacher.password_reset",
            Teacher::class,
            $teacher->id,
        );

        return back()->with("status", "Password guru berhasil direset.");
    }

    public function destroyTeacher(
        Request $request,
        Teacher $teacher,
    ): RedirectResponse {
        $teacherId = $teacher->id;
        $username = $teacher->user->username;
        $teacher->user()->delete();

        AuditLogger::log(
            $request->user(),
            "teacher.deleted",
            Teacher::class,
            $teacherId,
            ["username" => $username],
        );

        return back()->with("status", "Akun guru berhasil dihapus.");
    }

    public function students(): View
    {
        return view("admin.students", [
            "classes" => SchoolClass::query()->orderBy("name")->get(),
            "students" => Student::query()
                ->with(["user", "schoolClass"])
                ->latest()
                ->get(),
        ]);
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "name" => ["required", "string", "max:120"],
            "username" => [
                "required",
                "string",
                "max:50",
                "alpha_dash",
                "unique:users,username",
            ],
            "password" => ["required", "string", "min:6"],
            "class_id" => ["required", "exists:classes,id"],
            "nisn" => ["required", "digits:10", "unique:students,nisn"],
        ]);

        $user = User::query()->create([
            "name" => $validated["name"],
            "username" => $validated["username"],
            "role" => "student",
            "password" => Hash::make($validated["password"]),
        ]);

        $student = Student::query()->create([
            "user_id" => $user->id,
            "class_id" => (int) $validated["class_id"],
            "nisn" => $validated["nisn"],
        ]);

        AuditLogger::log(
            $request->user(),
            "student.created",
            Student::class,
            $student->id,
            ["username" => $user->username],
        );

        return back()->with("status", "Akun siswa berhasil dibuat.");
    }

    public function updateStudent(
        Request $request,
        Student $student,
    ): RedirectResponse {
        $validated = $request->validate([
            "name" => ["required", "string", "max:120"],
            "username" => [
                "required",
                "string",
                "max:50",
                "alpha_dash",
                "unique:users,username," . $student->user_id,
            ],
            "class_id" => ["required", "exists:classes,id"],
            "nisn" => [
                "required",
                "digits:10",
                "unique:students,nisn," . $student->id,
            ],
        ]);

        $student->user()->update([
            "name" => $validated["name"],
            "username" => $validated["username"],
        ]);

        $student->update([
            "class_id" => (int) $validated["class_id"],
            "nisn" => $validated["nisn"],
        ]);

        AuditLogger::log(
            $request->user(),
            "student.updated",
            Student::class,
            $student->id,
            ["username" => $validated["username"]],
        );

        return back()->with("status", "Akun siswa berhasil diperbarui.");
    }

    public function resetStudentPassword(
        Request $request,
        Student $student,
    ): RedirectResponse {
        $validated = $request->validate([
            "password" => ["required", "string", "min:6"],
        ]);

        $student->user()->update([
            "password" => Hash::make($validated["password"]),
        ]);

        AuditLogger::log(
            $request->user(),
            "student.password_reset",
            Student::class,
            $student->id,
        );

        return back()->with("status", "Password siswa berhasil direset.");
    }

    public function destroyStudent(
        Request $request,
        Student $student,
    ): RedirectResponse {
        $studentId = $student->id;
        $username = $student->user->username;
        $student->user()->delete();

        AuditLogger::log(
            $request->user(),
            "student.deleted",
            Student::class,
            $studentId,
            ["username" => $username],
        );

        return back()->with("status", "Akun siswa berhasil dihapus.");
    }


}
