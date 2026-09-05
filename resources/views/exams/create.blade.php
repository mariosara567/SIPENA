<x-layouts.app :title="'Buat Ujian - SIPENA'">
    <section class="card">
        <h1 style="margin:0 0 8px;">Buat Ujian Baru</h1>
        <p style="margin:0;color:#64748b;">Atur mapel, jadwal, durasi, token, dan status ujian.</p>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('exams.store') }}" class="grid two">
            @csrf
            <label>Judul Ujian
                <input name="title" value="{{ old('title') }}" required>
            </label>

            <label>Mata Pelajaran
                <select name="subject_id" required>
                    <option value="">Pilih mata pelajaran</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </label>

            @if ($isAdministrator)
                <label>Guru Pengampu
                    <select name="teacher_id" required>
                        <option value="">Pilih guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>{{ $teacher->user->name }}</option>
                        @endforeach
                    </select>
                </label>
            @else
                <input type="hidden" name="teacher_id" value="{{ $defaultTeacherId }}">
            @endif

            <label>Durasi (menit) — dihitung dari Mulai/Selesai
                <input type="number" min="1" max="600" id="exam_duration" value="" placeholder="Otomatis" readonly>
            </label>

            <label>Mulai Ujian
                <input type="datetime-local" id="exam_start_time" name="start_time" value="{{ old('start_time') }}" required>
            </label>

            <label>Selesai Ujian
                <input type="datetime-local" id="exam_end_time" name="end_time" value="{{ old('end_time') }}" required>
            </label>

            <label>Token Ujian
                <input name="token" value="{{ old('token') }}" required>
            </label>

            <label>Status
                <select name="status" required>
                    @foreach (['draft', 'scheduled', 'active', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(old('status', 'scheduled') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>

            <div style="grid-column:1/-1;display:flex;gap:10px;">
                <button class="btn btn-primary" type="submit">Simpan Ujian</button>
                <a class="btn btn-secondary" href="{{ route('exams.index') }}">Batal</a>
            </div>
        </form>
    </section>

    <script>
        const startInput = document.getElementById('exam_start_time');
        const endInput = document.getElementById('exam_end_time');
        const durationInput = document.getElementById('exam_duration');

        function updateExamDuration() {
            const startValue = startInput.value;
            const endValue = endInput.value;

            if (!startValue || !endValue) {
                durationInput.value = '';
                return;
            }

            const start = new Date(startValue);
            const end = new Date(endValue);

            if (isNaN(start.getTime()) || isNaN(end.getTime()) || end <= start) {
                durationInput.value = '';
                return;
            }

            const diffMinutes = Math.round((end - start) / 60000);
            durationInput.value = diffMinutes;
        }

        startInput.addEventListener('change', updateExamDuration);
        endInput.addEventListener('change', updateExamDuration);

        document.addEventListener('DOMContentLoaded', updateExamDuration);
    </script>
</x-layouts.app>
