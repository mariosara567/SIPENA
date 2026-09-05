<?php

namespace App\Http\Controllers;

use App\Jobs\SyncExamResultsJob;
use App\Models\ExamParticipant;
use App\Models\SyncLog;
use App\Support\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function index(): View
    {
        return view('sync.index', [
            'pendingCount' => ExamParticipant::query()->whereNotNull('finished_at')->where('sync_status', 'pending')->count(),
            'syncedCount' => ExamParticipant::query()->where('sync_status', 'synced')->count(),
            'logs' => SyncLog::query()->latest('sync_date')->limit(20)->get(),
        ]);
    }

    public function run(Request $request): RedirectResponse
    {
        SyncExamResultsJob::dispatch();
        AuditLogger::log($request->user(), 'sync.triggered', 'sync_logs', null);

        return back()->with('status', 'Proses sinkronisasi dimasukkan ke antrean.');
    }
}
