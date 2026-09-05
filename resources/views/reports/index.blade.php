<x-layouts.app :title="'Laporan Nilai - SIPENA'">
    <section class="card">
        <h2 style="margin-top:0;">Filter Laporan</h2>
        <form method="GET" action="{{ route('reports.index') }}" class="grid two">
            <label>Kelas
                <select name="class_id">
                    <option value="">Semua kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->display_name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Mata Pelajaran
                <select name="subject_id">
                    <option value="">Semua mapel</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected($subjectId === $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </label>
            <div style="display:flex;gap:8px;align-items:end;">
                <button class="btn btn-primary" type="submit">Terapkan</button>
                <a class="btn btn-secondary" href="{{ route('reports.export', ['class_id' => $classId, 'subject_id' => $subjectId]) }}">Export CSV</a>
                <a class="btn btn-secondary" href="{{ route('reports.export.pdf', ['class_id' => $classId, 'subject_id' => $subjectId]) }}">Export PDF</a>
            </div>
        </form>
    </section>

    <section class="card table-wrap">
        <table>
            <thead><tr><th>Siswa</th><th>NIS</th><th>Kelas</th><th>Mapel</th><th>Ujian</th><th>Nilai</th><th>Selesai</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->student_name }}</td>
                    <td>{{ $row->nis }}</td>
                    <td>{{ $row->class_name }}</td>
                    <td>{{ $row->subject_name }}</td>
                    <td>{{ $row->exam_title }}</td>
                    <td>{{ $row->score }}</td>
                    <td>{{ $row->finished_at?->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada data nilai.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.app>
