<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $role = auth()->user()->role;

        if ($role === "student") {
            return redirect()->route("student.exams.index");
        }

        $modules = [
            "administrator" => [
                "title" => "Dashboard Administrator",
                "subtitle" =>
                    "Kelola data sekolah, akun pengguna, sesi ujian, monitoring, dan laporan.",
                "items" => [
                    [
                        "name" => "Data Akademik",
                        "route" => "admin.academic.index",
                    ],
                    ["name" => "Akun Guru", "route" => "admin.teachers.index"],
                    ["name" => "Akun Siswa", "route" => "admin.students.index"],
                    ["name" => "Manajemen Ujian", "route" => "exams.index"],
                    ["name" => "Monitoring", "route" => "monitoring.index"],
                    ["name" => "Laporan Nilai", "route" => "reports.index"],
                ],
            ],
            "teacher" => [
                "title" => "Dashboard Guru",
                "subtitle" =>
                    "Susun bank soal, paket ujian, peserta, sesi, dan pantau hasil penilaian.",
                "items" => [
                    ["name" => "Ujian Saya", "route" => "exams.index"],
                    ["name" => "Monitoring", "route" => "monitoring.index"],
                    ["name" => "Laporan Nilai", "route" => "reports.index"],
                ],
            ],
            "student" => [
                "title" => "Dashboard Siswa",
                "subtitle" =>
                    "Masukkan token ujian, kerjakan soal, pantau waktu, dan kirim jawaban.",
                "items" => [
                    ["name" => "Ujian Saya", "route" => "student.exams.index"],
                ],
            ],
        ];

        return view("dashboard", [
            "module" => $modules[$role] ?? $modules["student"],
        ]);
    }
}
