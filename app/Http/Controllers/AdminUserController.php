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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminUserController extends Controller
{
    public function studentTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ["nama", "username", "nis", "kelas", "password"],
            [
                "Yohanes Carlos Ngaga Sara",
                "yohanes.carlos",
                "0123456789",
                "X - 1",
                "siswa123",
            ],
        ]);
        $sheet->getStyle("A1:E1")->getFont()->setBold(true);
        foreach (range("A", "E") as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save("php://output");
        }, "template-import-siswa-my_asssesmen.xlsx");
    }

    public function importStudents(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "students_file" => [
                "required",
                "file",
                "max:5120",
                "mimes:xlsx,xls,csv",
            ],
        ]);

        try {
            $rows = IOFactory::load($validated["students_file"]->getRealPath())
                ->getActiveSheet()
                ->toArray();
        } catch (Throwable) {
            return back()->withErrors([
                "students_file" =>
                    "File tidak dapat dibaca. Gunakan template Excel My Asssesmen.",
            ]);
        }

        $created = 0;
        $skipped = 0;
        foreach (array_slice($rows, 1) as $row) {
            [$name, $username, $nis, $className, $password] = array_pad(
                $row,
                5,
                null,
            );
            $name = trim((string) $name);
            $username = strtolower(trim((string) $username));
            $nis = trim((string) $nis);
            $className = trim((string) $className);
            $password = (string) $password;

            if (
                $name === "" ||
                $username === "" ||
                $nis === "" ||
                $className === "" ||
                strlen($password) < 6 ||
                !preg_match('/^[a-z0-9_-]+$/', $username)
            ) {
                $skipped++;
                continue;
            }

            $class = SchoolClass::query()->where("name", $className)->first();
            if (
                !$class ||
                User::query()->where("username", $username)->exists() ||
                Student::query()->where("nis", $nis)->exists()
            ) {
                $skipped++;
                continue;
            }

            DB::transaction(function () use (
                $name,
                $username,
                $nis,
                $class,
                $password,
            ): void {
                $user = User::query()->create([
                    "name" => $name,
                    "username" => $username,
                    "role" => "student",
                    "password" => Hash::make($password),
                ]);
                Student::query()->create([
                    "user_id" => $user->id,
                    "class_id" => $class->id,
                    "nis" => $nis,
                ]);
            });
            $created++;
        }

        AuditLogger::log(
            $request->user(),
            "student.imported",
            Student::class,
            null,
            ["created" => $created, "skipped" => $skipped],
        );

        return back()->with(
            "status",
            "$created siswa berhasil diimpor. $skipped baris dilewati.",
        );
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
            "nis" => ["required", "string", "max:30", "unique:students,nis"],
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
            "nis" => $validated["nis"],
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
            "nis" => [
                "required",
                "string",
                "max:30",
                "unique:students,nis," . $student->id,
            ],
        ]);

        $student->user()->update([
            "name" => $validated["name"],
            "username" => $validated["username"],
        ]);

        $student->update([
            "class_id" => (int) $validated["class_id"],
            "nis" => $validated["nis"],
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

    public function generateStudents(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "class_id" => ["required", "exists:classes,id"],
            "prefix" => ["required", "string", "max:12", "alpha_dash"],
            "start_number" => ["required", "integer", "min:1"],
            "count" => ["required", "integer", "min:1", "max:300"],
            "default_password" => ["required", "string", "min:6"],
            "nis_prefix" => ["required", "string", "max:12", "alpha_dash"],
        ]);

        $classId = (int) $validated["class_id"];
        $created = 0;
        $number = (int) $validated["start_number"];
        $target = (int) $validated["count"];

        DB::transaction(function () use (
            $validated,
            $classId,
            &$created,
            &$number,
            $target,
        ): void {
            while ($created < $target) {
                $suffix = str_pad((string) $number, 4, "0", STR_PAD_LEFT);
                $username = strtolower($validated["prefix"]) . $suffix;
                $nis = strtoupper($validated["nis_prefix"]) . $suffix;
                $number++;

                if (
                    User::query()->where("username", $username)->exists() ||
                    Student::query()->where("nis", $nis)->exists()
                ) {
                    continue;
                }

                $user = User::query()->create([
                    "name" => "Siswa " . $suffix,
                    "username" => $username,
                    "role" => "student",
                    "password" => Hash::make($validated["default_password"]),
                ]);

                Student::query()->create([
                    "user_id" => $user->id,
                    "class_id" => $classId,
                    "nis" => $nis,
                ]);

                $created++;
            }
        });

        AuditLogger::log(
            $request->user(),
            "student.bulk_generated",
            Student::class,
            null,
            [
                "class_id" => $classId,
                "count" => $created,
            ],
        );

        return back()->with(
            "status",
            $created . " akun siswa berhasil digenerate.",
        );
    }
}
