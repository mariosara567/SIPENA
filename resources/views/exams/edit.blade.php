<x-layouts.app :title="'Edit Ujian - My Asssesmen'">
<section class="card">
    <h1 style="margin:0 0 8px;">Edit Ujian</h1>
    <p style="color:#64748b;">Ujian yang sudah memiliki peserta mulai mengerjakan tidak dapat diubah.</p>
</section>
<section class="card">
<form method="POST" action="{{ route('exams.update',$exam) }}" class="grid two">
@csrf @method('PUT')
<label>Judul Ujian<input name="title" value="{{ old('title',$exam->title) }}" required></label>
<label>Mata Pelajaran<select name="subject_id" required>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id',$exam->subject_id)==$subject->id)>{{ $subject->name }}</option>@endforeach</select></label>
@if($isAdministrator)
<label>Guru Pengampu<select name="teacher_id" required><option value="">Pilih guru</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(old('teacher_id',$exam->teacher_id)==$teacher->id)>{{ $teacher->user->name }}</option>@endforeach</select></label>
@endif
<label>Mulai Ujian<input type="datetime-local" name="start_time" value="{{ old('start_time',$exam->start_time->format('Y-m-d\TH:i')) }}" required></label>
<label>Selesai Ujian<input type="datetime-local" name="end_time" value="{{ old('end_time',$exam->end_time->format('Y-m-d\TH:i')) }}" required></label>
<label>Status<select name="status" required>@foreach(['draft','scheduled','active','closed'] as $status)<option value="{{ $status }}" @selected(old('status',$exam->status)===$status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
<div style="grid-column:1/-1;display:flex;gap:10px;"><button class="btn btn-primary">Simpan Perubahan</button><a class="btn btn-secondary" href="{{ route('exams.show',$exam) }}">Batal</a></div>
</form>
</section>
</x-layouts.app>