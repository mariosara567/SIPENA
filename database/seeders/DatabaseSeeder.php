<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'Administrator My Asssesmen',
            'role' => 'administrator',
            'password' => Hash::make('password'),
        ]);

        $teacherUser = User::query()->firstOrCreate([
            'username' => 'guru1',
        ], [
            'name' => 'Guru Demo',
            'role' => 'teacher',
            'password' => Hash::make('password'),
        ]);

        Teacher::query()->firstOrCreate([
            'user_id' => $teacherUser->id,
        ]);

        $class = SchoolClass::query()->firstOrCreate([
            'name' => 'XII IPA 1',
        ]);

        $studentUser = User::query()->firstOrCreate([
            'username' => 'siswa1',
        ], [
            'name' => 'Siswa Demo',
            'role' => 'student',
            'password' => Hash::make('password'),
        ]);

        Student::query()->firstOrCreate([
            'user_id' => $studentUser->id,
        ], [
            'class_id' => $class->id,
            'nisn' => '2026000001',
        ]);

        Subject::query()->firstOrCreate([
            'name' => 'Matematika',
        ]);
    }
}
