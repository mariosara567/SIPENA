<?php

namespace Tests\Feature;

use App\Jobs\SyncExamResultsJob;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamParticipant;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncExamResultsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_job_marks_participant_as_synced(): void
    {
        config()->set('services.sipena_central.base_url', 'https://central.example.test');
        config()->set('services.sipena_central.token', 'secret-token');
        config()->set('services.sipena_central.school_code', 'SCH-01');

        Http::fake([
            'https://central.example.test/api/sync/exam-results' => Http::response(['ok' => true], 200),
        ]);

        $teacherUser = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::query()->create(['user_id' => $teacherUser->id]);
        $subject = Subject::query()->create(['name' => 'Kimia']);
        $class = SchoolClass::query()->create(['name' => 'X IPA 1']);
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::query()->create([
            'user_id' => $studentUser->id,
            'class_id' => $class->id,
            'nis' => '20261234',
        ]);

        $exam = Exam::query()->create([
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'title' => 'Ujian Kimia',
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
            'duration' => 60,
            'token' => 'TOK01',
            'status' => 'active',
        ]);

        $question = Question::query()->create([
            'exam_id' => $exam->id,
            'question' => 'H2O adalah?',
            'option_a' => 'Air',
            'option_b' => 'Api',
            'option_c' => 'Tanah',
            'option_d' => 'Udara',
            'option_e' => null,
            'correct_answer' => 'A',
            'score_weight' => 1,
        ]);

        $participant = ExamParticipant::query()->create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'started_at' => now()->subMinutes(40),
            'finished_at' => now()->subMinutes(10),
            'score' => 100,
            'sync_status' => 'pending',
        ]);

        Answer::query()->create([
            'participant_id' => $participant->id,
            'question_id' => $question->id,
            'answer' => 'A',
            'is_correct' => true,
        ]);

        (new SyncExamResultsJob())->handle();

        $this->assertDatabaseHas('exam_participants', [
            'id' => $participant->id,
            'sync_status' => 'synced',
        ]);
        $this->assertDatabaseHas('sync_logs', [
            'status' => 'success',
            'success_records' => 1,
        ]);
    }
}
