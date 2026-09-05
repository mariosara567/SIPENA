<x-layouts.app :title="'Tambah Peserta - SIPENA'">
    <section class="card">
        <h1 style="margin:0 0 8px;">Tambah Peserta Ujian</h1>
        <p style="margin:0;color:#64748b;">Ujian: {{ $exam->title }}</p>
    </section>

    <section class="card grid">
        <form method="GET" action="{{ route('exams.participants.create', $exam) }}" class="grid two">
            <label>Filter Kelas
                <select name="class_id">
                    <option value="">Semua kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected((string) $selectedClassId === (string) $class->id)>{{ $class->display_name }}</option>
                    @endforeach
                </select>
            </label>
            <div style="display:flex;align-items:end;">
                <button class="btn btn-secondary" type="submit">Terapkan Filter</button>
            </div>
        </form>

        <form method="POST" action="{{ route('exams.participants.store', $exam) }}" class="grid">
            @csrf
            @if ($selectedClassId)
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
            @endif

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Pilih</th>
                            <th>Nama</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}"></td>
                                <td>{{ $student->user->name }}</td>
                                <td>{{ $student->nis }}</td>
                                <td>{{ $student->schoolClass->display_name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Tidak ada data siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="display:flex;gap:10px;">
                <button class="btn btn-primary" type="submit">Tambahkan Peserta</button>
                <a class="btn btn-secondary" href="{{ route('exams.show', $exam) }}">Selesai</a>
            </div>
        </form>
    </section>
</x-layouts.app>
