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
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminUserController extends Controller
{
    public function studentTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Data Siswa");
        $sheet->fromArray([
            ["nama", "nisn", "kelas", "jenis_kelamin"],
            ["Yohanes Carlos Ngaga Sara", "0123456789", "", "L"],
        ]);

        // NISN as text to preserve leading zeros
        $sheet->getStyle("B:B")->getNumberFormat()->setFormatCode("@");

        // Load classes
        $classes    = SchoolClass::query()->orderBy("level")->orderBy("name")->get();
        $classCount = count($classes);

        // Write class list in hidden column E (same sheet = 100% reliable dropdown)
        foreach ($classes as $i => $class) {
            $sheet->setCellValue("E" . ($i + 1), $class->display_name);
        }

        // Write gender list in hidden column F
        $sheet->setCellValue("F1", "L");
        $sheet->setCellValue("F2", "P");

        // Hide helper columns E & F from view
        $sheet->getColumnDimension("E")->setVisible(false);
        $sheet->getColumnDimension("F")->setVisible(false);

        // Class dropdown using local hidden column E
        if ($classCount > 0) {
            $classValidation = new DataValidation();
            $classValidation->setType(DataValidation::TYPE_LIST);
            $classValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $classValidation->setAllowBlank(false);
            $classValidation->setShowDropDown(false); // false = SHOW the arrow in Excel
            $classValidation->setShowInputMessage(false);
            $classValidation->setShowErrorMessage(true);
            $classValidation->setErrorTitle("Kelas tidak valid");
            $classValidation->setError("Pilih kelas dari daftar dropdown yang tersedia.");
            $classValidation->setFormula1('$E$1:$E' . $classCount);
            for ($row = 2; $row <= 500; $row++) {
                $sheet->getCell("C{$row}")->setDataValidation(clone $classValidation);
            }
        }

        // Gender dropdown using local hidden column F
        $genderValidation = new DataValidation();
        $genderValidation->setType(DataValidation::TYPE_LIST);
        $genderValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $genderValidation->setAllowBlank(false);
        $genderValidation->setShowDropDown(false);
        $genderValidation->setShowInputMessage(false);
        $genderValidation->setShowErrorMessage(true);
        $genderValidation->setErrorTitle("Tidak valid");
        $genderValidation->setError("Pilih L (Laki-laki) atau P (Perempuan) dari dropdown.");
        $genderValidation->setFormula1('$F$1:$F$2');
        for ($row = 2; $row <= 500; $row++) {
            $sheet->getCell("D{$row}")->setDataValidation(clone $genderValidation);
        }

        // Header styling
        $sheet->getStyle("A1:D1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                             'color'       => ['argb' => 'FF1E40AF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        // Example row styling (italic so user knows it is a sample)
        $sheet->getStyle("A2:D2")->applyFromArray([
            'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => 'FFBFDBFE']]],
            'font'    => ['italic' => true],
        ]);

        // Column widths for visible columns only
        foreach (["A" => 36, "B" => 16, "C" => 28, "D" => 18] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $spreadsheet->setActiveSheetIndex(0);

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
                return back()->withErrors(["students_file" => "Kolom '{$required}' tidak ditemukan. Pastikan menggunakan template terbaru (ada kolom: nama, nisn, kelas, jenis_kelamin)."]);
            }
        }

        $map       = array_flip($header);
        $hasGender = isset($map["jenis_kelamin"]);
        $records   = [];
        $failures  = []; // collect per-row errors

        // Pre-load all classes once for performance
        $allClasses = SchoolClass::query()->get();

        foreach (array_slice($rows, 1) as $index => $row) {
            $excelRow   = $index + 2;
            $name       = trim((string)($row[$map["nama"]] ?? ""));
            $nisn       = preg_replace('/[^0-9]/', '', trim((string)($row[$map["nisn"]] ?? "")));
            $className  = trim((string)($row[$map["kelas"]] ?? ""));

            // Skip truly empty rows
            if ($name === "" && $nisn === "" && $className === "") continue;

            $rowErrors = [];

            // Validate name
            if ($name === "") $rowErrors[] = "nama wajib diisi";

            // Validate NISN exactly 10 digits
            if (!preg_match('/^\d{10}$/', $nisn)) {
                $rowErrors[] = "NISN harus tepat 10 digit angka (terdeteksi: '" . ($nisn ?: '-') . "')";
            }

            // Validate class
            $class = null;
            if ($className === "") {
                $rowErrors[] = "kelas wajib diisi";
            } else {
                $class = $allClasses->first(fn($c) => $c->display_name === $className || $c->name === $className);
                if (!$class) $rowErrors[] = "kelas '{$className}' tidak ditemukan di aplikasi";
            }

            // Validate gender
            $gender = $hasGender ? strtoupper(trim((string)($row[$map["jenis_kelamin"]] ?? ""))) : null;
            if ($gender !== null && $gender !== '' && !in_array($gender, ['L', 'P'])) {
                $rowErrors[] = "jenis kelamin harus L atau P (terdeteksi: '{$gender}')";
                $gender = null;
            }

            // Check duplicate NISN in file
            if (preg_match('/^\d{10}$/', $nisn)) {
                $isDupInFile = collect($records)->contains('nisn', $nisn);
                if ($isDupInFile) $rowErrors[] = "NISN {$nisn} duplikat dalam file ini";

                // Check duplicate in DB
                if (!$isDupInFile && Student::query()->where("nisn", $nisn)->exists()) {
                    $rowErrors[] = "NISN {$nisn} sudah terdaftar di sistem";
                }
                if (!$isDupInFile && User::query()->where("username", $nisn)->exists()) {
                    $rowErrors[] = "NISN {$nisn} sudah digunakan sebagai username lain";
                }
            }

            if (count($rowErrors) > 0) {
                $failures[] = "Baris {$excelRow} ({$name}): " . implode(", ", $rowErrors);
                continue; // skip this row, process others
            }

            $records[] = compact("name", "nisn", "class", "gender");
        }

        // Import valid records
        $created = 0;
        $importErrors = [];
        foreach ($records as $record) {
            try {
                DB::transaction(function () use ($record): void {
                    $user = User::query()->create([
                        "name"     => $record["name"],
                        "username" => $record["nisn"],
                        "role"     => "student",
                        "gender"   => $record["gender"],
                        "password" => Hash::make($record["nisn"]),
                    ]);
                    Student::query()->create([
                        "user_id"  => $user->id,
                        "class_id" => $record["class"]->id,
                        "nisn"     => $record["nisn"],
                    ]);
                });
                $created++;
            } catch (Throwable $e) {
                $importErrors[] = "Gagal import {$record['name']}: " . $e->getMessage();
            }
        }

        // Merge import runtime errors into failures
        $failures = array_merge($failures, $importErrors);

        AuditLogger::log($request->user(), "student.imported", Student::class, null, ["created" => $created, "failed" => count($failures)]);

        $summary = [
            'created'  => $created,
            'failed'   => count($failures),
            'failures' => $failures,
        ];

        return back()->with("status_import", json_encode($summary));
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
            "gender" => ["required", "in:L,P"],
            "nip" => ["nullable", "string", "digits:18", "unique:teachers,nip"],
        ]);

        $user = User::query()->create([
            "name" => $validated["name"],
            "username" => $validated["username"],
            "role" => "teacher",
            "gender" => $validated["gender"],
            "password" => Hash::make($validated["password"]),
        ]);

        $teacher = Teacher::query()->create([
            "user_id" => $user->id,
            "nip" => $validated["nip"],
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
            "gender" => ["required", "in:L,P"],
            "nip" => [
                "nullable",
                "string",
                "digits:18",
                "unique:teachers,nip," . $teacher->id,
            ],
            "password" => ["nullable", "string", "min:6"],
        ]);

        $teacher->user()->update([
            "name" => $validated["name"],
            "username" => $validated["username"],
            "gender" => $validated["gender"],
        ]);

        if (!empty($validated["password"])) {
            $teacher->user()->update([
                "password" => Hash::make($validated["password"]),
            ]);
        }

        $teacher->update([
            "nip" => $validated["nip"],
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
            "name"     => ["required", "string", "max:120"],
            "nisn"     => ["required", "digits:10", "unique:students,nisn"],
            "gender"   => ["required", "in:L,P"],
            "class_id" => ["required", "exists:classes,id"],
        ]);

        // Username dan password otomatis dari NISN
        $nisn = $validated["nisn"];
        if (User::query()->where("username", $nisn)->exists()) {
            return back()->withErrors(["nisn" => "NISN tersebut sudah terdaftar sebagai username."])->withInput();
        }

        $user = User::query()->create([
            "name"     => $validated["name"],
            "username" => $nisn,
            "role"     => "student",
            "gender"   => $validated["gender"],
            "password" => Hash::make($nisn),
        ]);

        $student = Student::query()->create([
            "user_id"  => $user->id,
            "class_id" => (int) $validated["class_id"],
            "nisn"     => $nisn,
        ]);

        AuditLogger::log(
            $request->user(),
            "student.created",
            Student::class,
            $student->id,
            ["username" => $user->username],
        );

        return back()->with("status_student_created", "Akun siswa {$validated['name']} berhasil dibuat! Username dan password: {$nisn}");
    }

    public function updateStudent(
        Request $request,
        Student $student,
    ): RedirectResponse {
        $validated = $request->validate([
            "name"     => ["required", "string", "max:120"],
            "gender"   => ["required", "in:L,P"],
            "class_id" => ["required", "exists:classes,id"],
            "nisn"     => [
                "required",
                "digits:10",
                "unique:students,nisn," . $student->id,
            ],
        ]);

        $nisn = $validated["nisn"];

        $student->user()->update([
            "name"   => $validated["name"],
            "gender" => $validated["gender"],
            // Keep username in sync with NISN if NISN changed
            "username" => $nisn,
        ]);

        $student->update([
            "class_id" => (int) $validated["class_id"],
            "nisn"     => $nisn,
        ]);

        AuditLogger::log(
            $request->user(),
            "student.updated",
            Student::class,
            $student->id,
            ["nisn" => $nisn],
        );

        return back()->with("status", "Data siswa berhasil diperbarui.");
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
