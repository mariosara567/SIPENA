<x-layouts.app :title="'Manajemen Ujian - SIPENA'">
    <section class="card" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
        <div>
            <h1 style="margin:0;">Manajemen Ujian</h1>
            <p style="margin:6px 0 0;color:#64748b;">Kelola jadwal, token, soal, dan peserta ujian.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('exams.create') }}">Buat Ujian</a>
    </section>

    <section class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Mapel</th>
                    <th>Guru</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                    <th>Token</th>
                    <th>Soal</th>
                    <th>Peserta</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($exams as $exam)
                    <tr>
                        <td>{{ $exam->title }}</td>
                        <td>{{ $exam->subject->name }}</td>
                        <td>{{ $exam->teacher->user->name }}</td>
                        <td>{{ $exam->start_time->format('d M Y H:i') }} - {{ $exam->end_time->format('H:i') }}</td>
                        <td>{{ ucfirst($exam->status) }}</td>
                        <td><strong style="letter-spacing:1px;color:var(--primary);">{{ $exam->token }}</strong></td>
                        <td>{{ $exam->questions_count }}</td>
                        <td>{{ $exam->participants_count }}</td>
                        <td><a class="btn btn-secondary" href="{{ route('exams.show', $exam) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9">Belum ada data ujian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.app>
