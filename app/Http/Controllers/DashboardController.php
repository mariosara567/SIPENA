<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = Auth::user();
        $role = $user->role;

        // Siswa langsung diarahkan ke daftar ujian
        if ($role === 'student') {
            return redirect()->route('student.exams.index');
        }

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ADMIN
        |--------------------------------------------------------------------------
        */
        if ($role === 'administrator') {
            $studentCount = Student::count();
            $teacherCount = Teacher::count();
            $examCount = Exam::count();

            $activeExamCount = Exam::where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->count();

            $module = [
                'title' => 'Dashboard Administrator',
                'subtitle' => 'Kelola data sekolah, akun pengguna, sesi ujian, monitoring, dan laporan.',

                'items' => [
                    [
                        'name' => 'Siswa',
                        'count' => $studentCount,
                        'route' => 'admin.students.index',
                        'icon' => 'fas fa-user-graduate',
                        'description' => 'Kelola data dan akun siswa.',
                    ],
                    [
                        'name' => 'Guru',
                        'count' => $teacherCount,
                        'route' => 'admin.teachers.index',
                        'icon' => 'fas fa-chalkboard-teacher',
                        'description' => 'Kelola data dan akun guru.',
                    ],
                    [
                        'name' => 'Ujian',
                        'count' => $examCount,
                        'route' => 'exams.index',
                        'icon' => 'fas fa-file-alt',
                        'description' => 'Kelola seluruh ujian.',
                    ],
                    [
                        'name' => 'Ujian Berlangsung',
                        'count' => $activeExamCount,
                        'route' => 'exams.index',
                        'icon' => 'fas fa-clock',
                        'description' => 'Ujian yang sedang berlangsung saat ini.',
                    ],
                    [
                        'name' => 'Data Akademik',
                        'route' => 'admin.academic.index',
                        'icon' => 'fas fa-school',
                        'description' => 'Kelola kelas dan mata pelajaran.',
                    ],
                    [
                        'name' => 'Monitoring',
                        'route' => 'monitoring.index',
                        'icon' => 'fas fa-desktop',
                        'description' => 'Pantau aktivitas ujian.',
                    ],
                    [
                        'name' => 'Laporan Nilai',
                        'route' => 'reports.index',
                        'icon' => 'fas fa-chart-bar',
                        'description' => 'Lihat dan kelola laporan nilai.',
                    ],
                ],
            ];

            return view('dashboard', [
                'module' => $module,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD GURU
        |--------------------------------------------------------------------------
        */
        if ($role === 'teacher') {
            $teacher = $user->teacher;

            $examQuery = Exam::query();

            if ($teacher) {
                $examQuery->where('teacher_id', $teacher->id);
            } else {
                $examQuery->whereRaw('1 = 0');
            }

            $examCount = (clone $examQuery)->count();

            $activeExamCount = (clone $examQuery)
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->count();

            return view('dashboard', [
                'module' => [
                    'title' => 'Dashboard Guru',
                    'subtitle' => 'Susun bank soal, paket ujian, peserta, sesi, dan pantau hasil penilaian.',
                    'items' => [
                        [
                            'name' => 'Ujian Saya',
                            'count' => $examCount,
                            'route' => 'exams.index',
                            'icon' => 'fas fa-file-alt',
                            'description' => 'Kelola ujian yang Anda buat.',
                        ],
                        [
                            'name' => 'Ujian Berlangsung',
                            'count' => $activeExamCount,
                            'route' => 'monitoring.index',
                            'icon' => 'fas fa-clock',
                            'description' => 'Ujian yang sedang berlangsung.',
                        ],
                        [
                            'name' => 'Monitoring',
                            'route' => 'monitoring.index',
                            'icon' => 'fas fa-desktop',
                            'description' => 'Pantau peserta ujian.',
                        ],
                        [
                            'name' => 'Laporan Nilai',
                            'route' => 'reports.index',
                            'icon' => 'fas fa-chart-bar',
                            'description' => 'Lihat hasil ujian.',
                        ],
                    ],
                ],
            ]);
        }

        return view('dashboard', [
            'module' => [
                'title' => 'Dashboard',
                'subtitle' => 'Selamat datang di My Asssesmen.',
                'items' => [],
            ],
        ]);
    }
}