<x-layouts.app :title="$exam->title.' - My Asssesmen'">
    <section class="card" style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;">
        <div>
            <h1 style="margin:0;">{{ $exam->title }}</h1>
            <p style="margin:8px 0;color:#64748b;">
                {{ $exam->subject->name }} | {{ $exam->teacher->user->name }} | {{ $exam->start_time->format('d M Y H:i') }} - {{ $exam->end_time->format('H:i') }}
            </p>
            <p style="margin:0;color:#334155;">Token: <strong>{{ $exam->token }}</strong> | Durasi: <strong>{{ $exam->duration }} menit</strong></p>
        </div>
        <div style="display:flex;gap:8px;">
            <a class="btn btn-secondary" href="{{ route('exams.questions.create', $exam) }}">Tambah Soal</a>
            <a class="btn btn-secondary" href="{{ route('exams.participants.create', $exam) }}">Tambah Peserta</a>
        </div>
    </section>

    <section class="card" style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
        <div style="flex:1;min-width:240px;"><h2 style="margin:0 0 5px;">Token Ujian</h2><p style="margin:0;color:#64748b;">Token 5 karakter dibuat otomatis oleh sistem dan menggunakan huruf besar.</p></div>
        <div style="padding:14px 20px;border:2px dashed var(--primary);border-radius:10px;font-size:24px;font-weight:900;letter-spacing:5px;color:var(--primary);">{{ $exam->token }}</div>
    </section>

    <section class="card">
        <div class="card-header">
            <div><h2 style="margin:0;">Impor Bank Soal Excel</h2><p>Gunakan template agar soal dan kunci jawaban terbaca otomatis.</p></div>
            <a class="btn btn-secondary" href="{{ route('exams.questions.template') }}"><i class="fas fa-file-arrow-down"></i> Download Template</a>
        </div>
        <form method="POST" action="{{ route('exams.questions.import', $exam) }}" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            @csrf
            <input type="file" name="questions_file" accept=".xlsx,.xls,.csv" required style="flex:1;">
            <button class="btn btn-primary" type="submit"><i class="fas fa-file-import"></i> Impor Soal</button>
        </form>
    </section>

    <section class="grid two">
        <article class="card table-wrap">
            <h2 style="margin-top:0;">Bank Soal ({{ $exam->questions->count() }})</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pertanyaan</th>
                        <th>Kunci</th>
                        <th>Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($exam->questions as $question)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $question->question }} @if($question->image_path)<br><img src="{{ asset('storage/'.$question->image_path) }}" alt="Gambar soal" style="max-width:180px;max-height:100px;object-fit:contain;margin-top:8px;border-radius:8px;">@endif</td>
                            <td>{{ $question->correct_answer }}</td>
                            <td>{{ $question->score_weight }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Belum ada soal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </article>

        <article class="card table-wrap">
            <h2 style="margin-top:0;">Peserta ({{ $exam->participants->count() }})</h2>
            <table>
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>NIS</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($exam->participants as $participant)
                        <tr>
                            <td>{{ $participant->student->user->name }}</td>
                            <td>{{ $participant->student->nisn }}</td>
                            <td>{{ $participant->started_at?->format('d M H:i') ?? '-' }}</td>
                            <td>{{ $participant->finished_at?->format('d M H:i') ?? '-' }}</td>
                            <td>{{ $participant->score ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada peserta.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </article>
    </section>
</x-layouts.app>
