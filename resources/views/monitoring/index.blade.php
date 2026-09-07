<x-layouts.app :title="'Monitoring Ujian - My Asssesmen'">
    <section class="grid two">
        <article class="card"><h3 style="margin:0;">Peserta Terdaftar</h3><p style="font-size:28px;margin:8px 0 0;">{{ $total }}</p></article>
        <article class="card"><h3 style="margin:0;">Peserta Online</h3><p style="font-size:28px;margin:8px 0 0;">{{ $online }}</p></article>
        <article class="card"><h3 style="margin:0;">Sedang Mengerjakan</h3><p style="font-size:28px;margin:8px 0 0;">{{ $inProgress }}</p></article>
        <article class="card"><h3 style="margin:0;">Selesai</h3><p style="font-size:28px;margin:8px 0 0;">{{ $completed }}</p></article>
    </section>

    <section class="card table-wrap">
        <h2 style="margin-top:0;">Progress per Ujian</h2>
        <table>
            <thead><tr><th>Ujian</th><th>Mapel</th><th>Guru</th><th>Peserta</th><th>Sedang</th><th>Selesai</th><th>Progress</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['exam']->title }}</td>
                    <td>{{ $row['exam']->subject->name }}</td>
                    <td>{{ $row['exam']->teacher->user->name }}</td>
                    <td>{{ $row['exam']->participants_count }}</td>
                    <td>{{ $row['exam']->in_progress_count }}</td>
                    <td>{{ $row['exam']->completed_count }}</td>
                    <td>{{ $row['progress'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada data monitoring.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="card table-wrap">
        <div class="card-header"><div><h2 style="margin:0;">Siswa yang Sedang Ujian</h2><p>Pantau pelanggaran perpindahan tab dan buka kembali akses siswa bila sudah diverifikasi.</p></div><span class="badge">Auto refresh 15 detik</span></div>
        <table>
            <thead><tr><th>Siswa</th><th>Ujian</th><th>Mulai</th><th>Pelanggaran</th><th>Status Akses</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($activeParticipants as $participant)
                <tr>
                    <td><strong>{{ $participant->student->user->name }}</strong><br><small>{{ $participant->student->nis }} · {{ $participant->student->schoolClass?->display_name }}</small></td>
                    <td>{{ $participant->exam->subject->name }}<br><small>{{ $participant->exam->title }}</small></td>
                    <td>{{ $participant->started_at?->format('H:i:s') }}</td>
                    <td><strong style="color:{{ $participant->violation_count ? '#dc2626' : '#16a34a' }}">{{ $participant->violation_count }} kali</strong><br><small>{{ $participant->locked_reason }}</small></td>
                    <td>{{ $participant->is_locked ? 'Terkunci' : 'Aktif' }}</td>
                    <td>
                        @if($participant->is_locked)
                            <form method="POST" action="{{ route('monitoring.unlock', $participant) }}">@csrf<button class="btn btn-primary" type="submit"><i class="fas fa-unlock"></i> Buka Kunci</button></form>
                        @else <span style="color:#16a34a;font-weight:700;">Aman</span> @endif
                    </td>
                </tr>
            @empty <tr><td colspan="6">Belum ada siswa yang sedang mengerjakan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
    <script>setTimeout(() => location.reload(), 15000);</script>
</x-layouts.app>
