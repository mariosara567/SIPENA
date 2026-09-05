<?php

namespace App\Jobs;

use App\Models\ExamParticipant;
use App\Models\SyncLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;

class SyncExamResultsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [30, 60, 120, 300];

    public function handle(): void
    {
        $endpoint = rtrim((string) config('services.sipena_central.base_url'), '/').'/api/sync/exam-results';
        $token = (string) config('services.sipena_central.token');

        if ($endpoint === '/api/sync/exam-results' || $token === '') {
            SyncLog::query()->create([
                'sync_date' => now(),
                'sync_type' => 'exam_results',
                'total_records' => 0,
                'success_records' => 0,
                'failed_records' => 0,
                'status' => 'failed',
                'message' => 'Konfigurasi SIPENA_CENTRAL_BASE_URL atau SIPENA_CENTRAL_TOKEN belum lengkap.',
            ]);

            return;
        }

        $participants = ExamParticipant::query()
            ->whereNotNull('finished_at')
            ->where('sync_status', 'pending')
            ->with([
                'exam.subject',
                'student.user',
                'student.schoolClass',
                'answers.question',
            ])
            ->limit(100)
            ->get();

        if ($participants->isEmpty()) {
            SyncLog::query()->create([
                'sync_date' => now(),
                'sync_type' => 'exam_results',
                'total_records' => 0,
                'success_records' => 0,
                'failed_records' => 0,
                'status' => 'success',
                'message' => 'Tidak ada data pending untuk disinkronkan.',
            ]);

            return;
        }

        $payload = $participants->map(function (ExamParticipant $participant) {
            return [
                'participant_id' => $participant->id,
                'exam' => [
                    'id' => $participant->exam->id,
                    'title' => $participant->exam->title,
                    'subject' => $participant->exam->subject->name,
                ],
                'student' => [
                    'id' => $participant->student->id,
                    'name' => $participant->student->user->name,
                    'nis' => $participant->student->nis,
                    'class' => $participant->student->schoolClass->name,
                ],
                'score' => $participant->score,
                'started_at' => $participant->started_at?->toIso8601String(),
                'finished_at' => $participant->finished_at?->toIso8601String(),
                'answers' => $participant->answers->map(fn ($answer) => [
                    'question_id' => $answer->question_id,
                    'answer' => $answer->answer,
                    'is_correct' => $answer->is_correct,
                ])->values()->all(),
            ];
        })->values()->all();

        $response = Http::timeout(20)
            ->withToken($token)
            ->acceptJson()
            ->post($endpoint, [
                'school_code' => (string) config('services.sipena_central.school_code'),
                'synced_at' => now()->toIso8601String(),
                'records' => $payload,
            ]);

        if (! $response->successful()) {
            SyncLog::query()->create([
                'sync_date' => now(),
                'sync_type' => 'exam_results',
                'total_records' => count($payload),
                'success_records' => 0,
                'failed_records' => count($payload),
                'status' => 'failed',
                'message' => 'Sinkronisasi gagal: '.$response->status().' '.$response->body(),
            ]);

            throw new \RuntimeException('Sinkronisasi gagal dengan status '.$response->status());
        }

        ExamParticipant::query()
            ->whereIn('id', $participants->pluck('id'))
            ->update(['sync_status' => 'synced']);

        SyncLog::query()->create([
            'sync_date' => now(),
            'sync_type' => 'exam_results',
            'total_records' => count($payload),
            'success_records' => count($payload),
            'failed_records' => 0,
            'status' => 'success',
            'message' => 'Sinkronisasi hasil ujian berhasil.',
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        SyncLog::query()->create([
            'sync_date' => now(),
            'sync_type' => 'exam_results',
            'total_records' => 0,
            'success_records' => 0,
            'failed_records' => 0,
            'status' => 'failed',
            'message' => 'Job sinkronisasi gagal: '.($exception?->getMessage() ?? 'Unknown error'),
        ]);
    }
}
