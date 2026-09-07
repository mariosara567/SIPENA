<x-layouts.app :title="'Edit Soal - My Asssesmen'">
<section class="card"><h1 style="margin:0 0 8px;">Edit Soal</h1><p style="color:#64748b;">Ujian: {{ $exam->title }}</p></section>
<section class="card">
<form method="POST" action="{{ route('exams.questions.update',[$exam,$question]) }}" class="grid" enctype="multipart/form-data">
@csrf @method('PUT')
<label>Pertanyaan<textarea name="question" required style="min-height:150px;">{{ old('question',$question->question) }}</textarea></label>
@if($question->image_path)
<div><p style="font-weight:700;font-size:13px;">Gambar saat ini</p><img src="{{ asset('storage/'.$question->image_path) }}" style="max-width:320px;max-height:200px;border-radius:10px;margin-top:8px;"></div>
<label style="display:flex;align-items:center;gap:8px;font-weight:600;"><input type="checkbox" name="remove_image" value="1" style="width:auto;"> Hapus gambar</label>
@endif
<label>Ganti / Tambah Gambar (opsional)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, WebP maksimal 2 MB.</small></label>
<div style="display:grid;gap:12px;">@foreach(['A','B','C','D','E'] as $option)
<label style="display:grid;grid-template-columns:38px 1fr;gap:10px;align-items:center;"><input type="radio" name="correct_answer" value="{{ $option }}" @checked(old('correct_answer',$question->correct_answer)===$option) style="width:22px;"><input name="option_{{ strtolower($option) }}" value="{{ old('option_'.strtolower($option),$question->{'option_'.strtolower($option)}) }}" placeholder="Pilihan {{ $option }}" @required($option!=='E')></label>
@endforeach</div>
<label>Bobot Nilai<input type="number" min="0.1" step="0.1" name="score_weight" value="{{ old('score_weight',$question->score_weight) }}" required></label>
<div style="display:flex;gap:10px;"><button class="btn btn-primary">Simpan Perubahan</button><a class="btn btn-secondary" href="{{ route('exams.show',$exam) }}">Batal</a></div>
</form>
</section></x-layouts.app>