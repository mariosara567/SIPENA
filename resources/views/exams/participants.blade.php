<x-layouts.app :title="'Tambah Peserta - My Asssesmen'">
    <a href="{{ route('exams.show', $exam) }}" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; color:#64748b; font-weight:600; font-size:15px; margin-bottom:16px; transition:color 0.2s;">
        <span style="display:flex; align-items:center; justify-content:center; width:24px; height:24px; background:var(--primary); color:white; border-radius:50%; font-size:12px;">
            <i class="fas fa-arrow-left"></i>
        </span>
        Kembali ke Detail Ujian
    </a>

    <section class="card" style="padding:20px 24px;">
        <h1 style="margin:0 0 4px;">Tambah Peserta Ujian</h1>
        <p style="margin:0;color:#64748b; font-size:14px;">Ujian: <strong>{{ $exam->title }}</strong></p>
        @if(isset($enrolledCount) && $enrolledCount > 0)
            <div style="margin-top:10px; display:inline-flex; align-items:center; gap:6px; background:#eff6ff; color:#2563eb; padding:5px 12px; border-radius:99px; font-size:13px; font-weight:600;">
                <i class="fas fa-info-circle"></i>
                {{ $enrolledCount }} siswa sudah terdaftar dan tidak ditampilkan di bawah
            </div>
        @endif
    </section>

    <section class="card grid" style="margin-top:16px;">
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
                                <td>{{ $student->nisn }}</td>
                                <td>{{ $student->schoolClass->display_name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:24px; text-align:center; color:#64748b;">
                                    <i class="fas fa-check-circle" style="color:#22c55e; margin-right:6px;"></i>
                                    Semua siswa di kelas ini sudah terdaftar sebagai peserta ujian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->isNotEmpty())
                <div style="display:flex;gap:10px;">
                    <button class="btn btn-primary" type="submit">Tambahkan Peserta</button>
                    <a class="btn btn-secondary" href="{{ route('exams.show', $exam) }}">Selesai</a>
                </div>
            @else
                <div>
                    <a class="btn btn-secondary" href="{{ route('exams.show', $exam) }}">Kembali ke Detail Ujian</a>
                </div>
            @endif
        </form>
    </section>
</x-layouts.app>
