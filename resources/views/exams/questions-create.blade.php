<x-layouts.app :title="'Tambah Soal - My Asssesmen'">
    <a href="{{ route('exams.show', $exam) }}" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; color:#64748b; font-weight:600; font-size:15px; margin-bottom:16px; transition:color 0.2s;">
        <span style="display:flex; align-items:center; justify-content:center; width:24px; height:24px; background:var(--primary); color:white; border-radius:50%; font-size:12px;">
            <i class="fas fa-arrow-left"></i>
        </span>
        Kembali ke Detail Ujian
    </a>

    {{-- Header with blue accent --}}
    <div class="gform-card">
        <div class="gform-card-header">
            <h2><i class="fas fa-pen-to-square" style="margin-right:8px;"></i> Form Soal & Jawaban</h2>
            <p>{{ $exam->title }} — {{ $exam->subject->name ?? '' }}</p>
        </div>
    </div>

    {{-- Question Form Card --}}
    <div class="gform-card active">
        <div class="gform-card-body">
            <form method="POST" action="{{ route('exams.questions.store', $exam) }}" enctype="multipart/form-data">
                @csrf

                {{-- Question Text --}}
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px; margin-bottom:6px;">Pertanyaan</label>
                    <textarea name="question" placeholder="Ketik butir pertanyaan di sini..." style="font-size:16px; min-height:120px; border:none; border-bottom:3px solid var(--primary); border-radius:0; padding:12px 4px; resize:vertical;" required>{{ old('question') }}</textarea>
                </div>

                {{-- Image Upload --}}
                <div style="margin-bottom:20px; padding:16px; background:var(--bg); border-radius:10px; border:1px solid var(--border);">
                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <label style="margin:0; cursor:pointer; display:flex; align-items:center; gap:8px; font-size:13px; color:var(--primary); font-weight:600;">
                            <i class="fas fa-image"></i> Unggah Gambar/Rumus (opsional)
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="previewImage(this)">
                        </label>
                        <small style="color:#94a3b8; font-size:12px;">JPG, PNG, WebP. Maks 2 MB.</small>
                    </div>
                    <div id="imagePreview" style="display:none; margin-top:12px;">
                        <img id="previewImg" src="" alt="Preview" style="max-width:300px; max-height:180px; border-radius:8px; border:2px solid var(--border); object-fit:contain;">
                    </div>
                </div>

                {{-- Answer Options — Google Form style --}}
                <div style="margin-bottom:20px;">
                    <label style="font-size:13px; margin-bottom:10px;">Pilihan Jawaban</label>
                    <div style="display:grid; gap:4px;">
                        @foreach (['A','B','C','D','E'] as $option)
                            <div class="gform-option">
                                <input type="radio" name="correct_answer" value="{{ $option }}" title="Jadikan jawaban benar" @checked(old('correct_answer','A') === $option)>
                                <div class="opt-label">{{ $option }}</div>
                                <input type="text" name="option_{{ strtolower($option) }}" value="{{ old('option_'.strtolower($option)) }}" placeholder="Pilihan {{ $option }}{{ $option === 'E' ? ' (opsional)' : '' }}" @required($option !== 'E')>
                            </div>
                        @endforeach
                    </div>
                    <small style="color:#64748b; margin-top:8px; display:block; font-size:12px;">
                        <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                        Klik tombol radio di kiri untuk menentukan kunci jawaban benar.
                    </small>
                </div>

                {{-- Score Weight --}}
                <div style="margin-bottom:24px; max-width:200px;">
                    <label style="font-size:13px;">Bobot Nilai</label>
                    <input type="number" min="0.1" step="0.1" name="score_weight" value="{{ old('score_weight', 1) }}" required>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex; gap:10px; padding-top:20px; border-top:2px solid var(--border);">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-save"></i> Simpan Soal
                    </button>
                    <a class="btn btn-secondary" href="{{ route('exams.show', $exam) }}">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a class="btn btn-secondary" href="{{ route('exams.questions.template') }}" style="margin-left:auto;">
                        <i class="fas fa-download"></i> Template Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const img = document.getElementById('previewImg');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</x-layouts.app>
