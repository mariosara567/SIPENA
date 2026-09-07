<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAcademicController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamManagementController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ParticipantManagementController;
use App\Http\Controllers\QuestionManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentExamController;
use App\Http\Controllers\TeacherProfileController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
})->name("home");

Route::middleware("guest")->group(function () {
    Route::get("/login", [AuthController::class, "create"])->name("login");
    Route::post("/login", [AuthController::class, "store"])->name(
        "login.store",
    );
});

Route::middleware("auth")->group(function () {
    Route::get("/dashboard", DashboardController::class)->name("dashboard");
    Route::post("/logout", [AuthController::class, "destroy"])->name("logout");

    Route::middleware("role:administrator,teacher")->group(function () {
        Route::get("/exams", [ExamManagementController::class, "index"])->name(
            "exams.index",
        );
        Route::get("/exams/create", [
            ExamManagementController::class,
            "create",
        ])->name("exams.create");
        Route::post("/exams", [ExamManagementController::class, "store"])->name(
            "exams.store",
        );
        Route::get("/exams/{exam}", [
            ExamManagementController::class,
            "show",
        ])->name("exams.show");
Route::get("/exams/{exam}/questions/create", [
            QuestionManagementController::class,
            "create",
        ])->name("exams.questions.create");
        Route::post("/exams/{exam}/questions", [
            QuestionManagementController::class,
            "store",
        ])->name("exams.questions.store");

        Route::get("/exams/{exam}/participants", [
            ParticipantManagementController::class,
            "create",
        ])->name("exams.participants.create");
        Route::post("/exams/{exam}/participants", [
            ParticipantManagementController::class,
            "store",
        ])->name("exams.participants.store");

        Route::get("/monitoring", [MonitoringController::class, "index"])->name(
            "monitoring.index",
        );
        Route::post("/monitoring/participants/{participant}/unlock", [
            MonitoringController::class,
            "unlock",
        ])->name("monitoring.unlock");
        Route::get("/reports", [ReportController::class, "index"])->name(
            "reports.index",
        );
        Route::get("/reports/export", [
            ReportController::class,
            "export",
        ])->name("reports.export");
        Route::get("/reports/export-pdf", [
            ReportController::class,
            "exportPdf",
        ])->name("reports.export.pdf");

        Route::post("/exams/{exam}/questions/import", [
            QuestionManagementController::class,
            "import",
        ])->name("exams.questions.import");
        Route::get("/exams/questions/template", [
            QuestionManagementController::class,
            "template",
        ])->name("exams.questions.template");
    });

    Route::middleware("role:administrator")->group(function () {
        Route::get("/admin/academic", [
            AdminAcademicController::class,
            "index",
        ])->name("admin.academic.index");
        Route::post("/admin/classes", [
            AdminAcademicController::class,
            "storeClass",
        ])->name("admin.classes.store");
        Route::put("/admin/classes/{class}", [
            AdminAcademicController::class,
            "updateClass",
        ])->name("admin.classes.update");
        Route::delete("/admin/classes/{class}", [
            AdminAcademicController::class,
            "destroyClass",
        ])->name("admin.classes.destroy");
        Route::post("/admin/subjects", [
            AdminAcademicController::class,
            "storeSubject",
        ])->name("admin.subjects.store");
        Route::put("/admin/subjects/{subject}", [
            AdminAcademicController::class,
            "updateSubject",
        ])->name("admin.subjects.update");
        Route::delete("/admin/subjects/{subject}", [
            AdminAcademicController::class,
            "destroySubject",
        ])->name("admin.subjects.destroy");

        Route::get("/admin/teachers", [
            AdminUserController::class,
            "teachers",
        ])->name("admin.teachers.index");
        Route::post("/admin/teachers", [
            AdminUserController::class,
            "storeTeacher",
        ])->name("admin.teachers.store");
        Route::get("/admin/teachers/template", [
            AdminUserController::class,
            "teacherTemplate",
        ])->name("admin.teachers.template");
        Route::post("/admin/teachers/import", [
            AdminUserController::class,
            "importTeachers",
        ])->name("admin.teachers.import");
        Route::put("/admin/teachers/{teacher}", [
            AdminUserController::class,
            "updateTeacher",
        ])->name("admin.teachers.update");
        Route::post("/admin/teachers/{teacher}/reset-password", [
            AdminUserController::class,
            "resetTeacherPassword",
        ])->name("admin.teachers.reset_password");
        Route::delete("/admin/teachers/{teacher}", [
            AdminUserController::class,
            "destroyTeacher",
        ])->name("admin.teachers.destroy");

        Route::get("/admin/students", [
            AdminUserController::class,
            "students",
        ])->name("admin.students.index");
        Route::post("/admin/students", [
            AdminUserController::class,
            "storeStudent",
        ])->name("admin.students.store");
        Route::put("/admin/students/{student}", [
            AdminUserController::class,
            "updateStudent",
        ])->name("admin.students.update");
        Route::post("/admin/students/{student}/reset-password", [
            AdminUserController::class,
            "resetStudentPassword",
        ])->name("admin.students.reset_password");
        Route::delete("/admin/students/{student}", [
            AdminUserController::class,
            "destroyStudent",
        ])->name("admin.students.destroy");
Route::get("/admin/students/template", [
            AdminUserController::class,
            "studentTemplate",
        ])->name("admin.students.template");
        Route::post("/admin/students/import", [
            AdminUserController::class,
            "importStudents",
        ])->name("admin.students.import");

    });

    Route::middleware("role:teacher")->group(function () {
        Route::get("/profile", [TeacherProfileController::class, "edit"])->name("teacher.profile.edit");
        Route::put("/profile", [TeacherProfileController::class, "update"])->name("teacher.profile.update");
    });

    Route::middleware("role:student")->group(function () {
        Route::get("/my-exams", [StudentExamController::class, "index"])->name(
            "student.exams.index",
        );
        Route::post("/my-exams/{participant}/start", [
            StudentExamController::class,
            "start",
        ])->name("student.exams.start");
        Route::get("/my-exams/{participant}/take", [
            StudentExamController::class,
            "take",
        ])->name("student.exams.take");
        Route::post("/my-exams/{participant}/answers", [
            StudentExamController::class,
            "saveAnswer",
        ])->name("student.exams.answers.save");
        Route::post("/my-exams/{participant}/violation", [
            StudentExamController::class,
            "reportViolation",
        ])->name("student.exams.violation");
        Route::post("/my-exams/{participant}/submit", [
            StudentExamController::class,
            "submit",
        ])->name("student.exams.submit");
    });
});
