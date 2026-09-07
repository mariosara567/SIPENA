<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamParticipant;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_exam(): void
    {
        $teacherUser = User::factory()->create(["role" => "teacher"]);
        $teacher = Teacher::query()->create(["user_id" => $teacherUser->id]);
        $subject = Subject::query()->create(["name" => "Fisika"]);

        $response = $this->actingAs($teacherUser)->post(route("exams.store"), [
            "subject_id" => $subject->id,
            "title" => "Ujian Fisika 1",
            "start_time" => now()->addHour()->format("Y-m-d H:i:s"),
            "end_time" => now()->addHours(2)->format("Y-m-d H:i:s"),
            "duration" => 90,
            "token" => "ABC12",
            "status" => "scheduled",
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas("exams", [
            "title" => "Ujian Fisika 1",
            "teacher_id" => $teacher->id,
            "token" => "ABC12",
        ]);
    }

    public function test_student_submit_generates_score(): void
    {
        $teacherUser = User::factory()->create(["role" => "teacher"]);
        $teacher = Teacher::query()->create(["user_id" => $teacherUser->id]);
        $subject = Subject::query()->create(["name" => "Matematika"]);

        $exam = Exam::query()->create([
            "subject_id" => $subject->id,
            "teacher_id" => $teacher->id,
            "title" => "Ujian Aljabar",
            "start_time" => now()->subMinutes(15),
            "end_time" => now()->addMinutes(60),
            "duration" => 30,
            "token" => "MATH1",
            "status" => "active",
            "question_count" => 2,
        ]);

        $question1 = Question::query()->create([
            "exam_id" => $exam->id,
            "question" => "2 + 2 = ?",
            "option_a" => "3",
            "option_b" => "4",
            "option_c" => "5",
            "option_d" => "6",
            "option_e" => null,
            "correct_answer" => "B",
            "score_weight" => 1,
        ]);

        Question::query()->create([
            "exam_id" => $exam->id,
            "question" => "5 x 2 = ?",
            "option_a" => "10",
            "option_b" => "8",
            "option_c" => "7",
            "option_d" => "5",
            "option_e" => null,
            "correct_answer" => "A",
            "score_weight" => 1,
        ]);

        $class = SchoolClass::query()->create(["name" => "XII IPS 1"]);
        $studentUser = User::factory()->create(["role" => "student"]);
        $student = Student::query()->create([
            "user_id" => $studentUser->id,
            "class_id" => $class->id,
            "nisn" => "2026000001",
        ]);

        $participant = ExamParticipant::query()->create([
            "exam_id" => $exam->id,
            "student_id" => $student->id,
        ]);

        $this->actingAs($studentUser)
            ->post(route("student.exams.start", $participant), [
                "token" => "MATH1",
            ])
            ->assertRedirect(route("student.exams.take", $participant));

        $this->actingAs($studentUser)
            ->postJson(route("student.exams.answers.save", $participant), [
                "question_id" => $question1->id,
                "answer" => "B",
            ])
            ->assertOk();

        $this->actingAs($studentUser)
            ->post(route("student.exams.submit", $participant))
            ->assertRedirect(route("student.exams.index"));

        $this->assertDatabaseHas("exam_participants", [
            "id" => $participant->id,
            "score" => 50,
        ]);
    }

    public function test_tab_violation_locks_student_and_teacher_can_unlock_session(): void
    {
        $teacherUser = User::factory()->create(["role" => "teacher"]);
        $teacher = Teacher::query()->create(["user_id" => $teacherUser->id]);
        $subject = Subject::query()->create(["name" => "Informatika"]);
        $exam = Exam::query()->create([
            "subject_id" => $subject->id,
            "teacher_id" => $teacher->id,
            "title" => "UTS",
            "start_time" => now()->subMinute(),
            "end_time" => now()->addHour(),
            "duration" => 60,
            "token" => "ABC123",
            "status" => "active",
            "question_count" => 0,
        ]);
        $class = SchoolClass::query()->create(["name" => "X - 1"]);
        $studentUser = User::factory()->create(["role" => "student"]);
        $student = Student::query()->create([
            "user_id" => $studentUser->id,
            "class_id" => $class->id,
            "nisn" => "2026000002",
        ]);
        $participant = ExamParticipant::query()->create([
            "exam_id" => $exam->id,
            "student_id" => $student->id,
            "started_at" => now(),
        ]);

        $this->actingAs($studentUser)
            ->postJson(route("student.exams.violation", $participant), [
                "reason" => "Membuka tab lain",
            ])
            ->assertOk()
            ->assertJson(["locked" => true]);

        $this->assertDatabaseHas("exam_participants", [
            "id" => $participant->id,
            "is_locked" => true,
            "violation_count" => 1,
        ]);

        $this->actingAs($teacherUser)
            ->post(route("monitoring.unlock", $participant))
            ->assertRedirect();

        $this->assertDatabaseHas("exam_participants", [
            "id" => $participant->id,
            "is_locked" => false,
        ]);
    }
}
