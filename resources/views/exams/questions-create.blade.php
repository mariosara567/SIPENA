<x-layouts.app :title="'Tambah Soal - My Asssesmen'">
    <section class="card" style="border-top:7px solid var(--primary);">
        <div style="display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap;">
            <div><h1 style="margin:0 0 8px;">Form Soal & Jawaban</h1><p style="margin:0;color:#64748b;">Ujian: {{ $exam->title }} — isi seperti membuat pertanyaan di Google Form.</p></div>
            <a class="btn btn-secondary" href="{{ route('exams.questions.template') }}"><i class="fas fa-download"></i> Template Excel Soal</a>
        </div>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('exams.questions.store', $exam) }}" class="grid">
            @csrf
            <label style="font-size:15px;">Pertanyaan<textarea name="question" placeholder="Tulis pertanyaan..." style="font-size:16px;min-height:150px;" required>{{ old('question') }}</textarea></label>
            <div style="display:grid;gap:12px;">
                @foreach (['A','B','C','D','E'] as $option)
                    <label style="display:grid;grid-template-columns:38px 1fr;gap:10px;align-items:center;margin:0;">
                        <input type="radio" name="correct_answer" value="{{ $option }}" title="Jadikan jawaban benar" style="width:22px;min-height:22px;justify-self:center;" @checked(old('correct_answer','A') === $option)>
                        <input name="option_{{ strtolower($option) }}" value="{{ old('option_'.strtolower($option)) }}" placeholder="Pilihan {{ $option }}{{ $option === 'E' ? ' (opsional)' : '' }}" @required($option !== 'E')>
                    </label>
                @endforeach
                <small style="color:#64748b;margin-left:48px;">Pilih lingkaran di kiri untuk menentukan kunci jawaban.</small>
            </div>
            <label>Bobot Nilai
                <input type="number" min="0.1" step="0.1" name="score_weight" value="{{ old('score_weight', 1) }}" required>
            </label>
            <div style="display:flex;gap:10px;">
                <button class="btn btn-primary" type="submit">Simpan Soal</button>
                <a class="btn btn-secondary" href="{{ route('exams.show', $exam) }}">Kembali</a>
            </div>
        </form>
    </section>
</x-layouts.app>
