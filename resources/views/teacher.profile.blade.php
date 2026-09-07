<x-layouts.app :title="'Profil Guru - My Asssesmen'">
<section class="card"><h1 style="margin:0 0 6px;">Profil Guru</h1><p>Guru dapat memperbarui nama, username, NIP, mata pelajaran, dan password sendiri.</p></section>
<section class="card"><form method="POST" action="{{ route('teacher.profile.update') }}" class="grid two">@csrf @method('PUT')
<label>Nama<input name="name" value="{{ old('name',$teacher->user->name) }}" required></label>
<label>Username<input name="username" value="{{ old('username',$teacher->user->username) }}" required></label>
<label>NIP (opsional, 18 digit)<input name="nip" inputmode="numeric" pattern="\d{18}" maxlength="18" value="{{ old('nip',$teacher->nip) }}"></label>
<label>Mata Pelajaran<input name="subject" value="{{ old('subject',$teacher->subject) }}"></label>
<label>Password Baru (opsional)<input type="password" name="password" minlength="6"></label>
<label>Konfirmasi Password<input type="password" name="password_confirmation" minlength="6"></label>
<div style="grid-column:1/-1;display:flex;gap:10px;"><button class="btn btn-primary" type="submit">Simpan Perubahan</button><a class="btn btn-secondary" href="{{ route('dashboard') }}">Kembali</a></div>
</form></section>
</x-layouts.app>
