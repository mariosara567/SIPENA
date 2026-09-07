<x-layouts.student :title="'Ujian Saya - My Asssesmen'">
    <section class="card" style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap;">
        <div><h1 style="margin:0 0 8px;">Portal Ujian Siswa</h1><p>Jadwal ujian ditampilkan berdasarkan waktu server WIB. Masuk hanya pada tanggal dan jam yang ditentukan.</p></div>
        <span class="badge" style="background:#dcfce7;color:#166534;"><i class="fas fa-circle-check"></i> Siswa Aktif</span>
    </section>

    <section class="card" style="display:flex;gap:18px;align-items:center;flex-wrap:wrap;">
        <div style="width:82px;height:82px;border-radius:50%;display:grid;place-items:center;background:rgba(15,118,110,.12);color:var(--primary);font-size:36px;flex:0 0 82px;"><i class="fas fa-user-graduate"></i></div>
        <div style="flex:1;min-width:220px;"><h2 style="margin:0 0 5px;">{{ auth()->user()->name }}</h2><p>NISN: <strong>{{ auth()->user()->student->nisn }}</strong></p><p>Kelas: <strong>{{ auth()->user()->student->schoolClass?->display_name ?? '-' }}</strong></p></div>
        <div style="padding-left:18px;border-left:2px solid var(--border);min-width:260px;max-width:420px;"><strong>Catatan</strong><p style="color:var(--muted);font-size:13px;margin-top:5px;">Token diberikan oleh guru/pengawas. Token dibuat otomatis oleh sistem.</p></div>
    </section>

    @forelse($participants as $participant)
        @php($exam = $participant->exam)
        @php($now = now())
        @php($isUpcoming = !$participant->finished_at && $now->lt($exam->start_time))
        @php($isLive = !$participant->finished_at && $now->gte($exam->start_time) && $now->lt($exam->end_time))
        @php($isExpired = !$participant->finished_at && $now->gte($exam->end_time))
        @php($isLate = $isLive && $participant->started_at && $participant->started_at->gt($exam->start_time))
        @php($lateMinutes = $isLate ? $exam->start_time->diffInMinutes($participant->started_at) : 0)
        @php($status = $participant->finished_at ? 'Selesai' : ($participant->is_locked ? 'Terkunci' : ($isLive && $participant->started_at ? 'Sedang Berjalan' : ($isLive ? 'Sedang Berlangsung' : ($isUpcoming ? 'Belum Dimulai' : 'Jadwal Berakhir')))))
        <article class="card" style="border-top:5px solid {{ $participant->finished_at ? 'var(--success)' : ($isLive ? 'var(--primary)' : 'var(--border)') }};">
            <div style="display:flex;justify-content:space-between;gap:15px;align-items:flex-start;flex-wrap:wrap;">
                <div><h2 style="margin:0 0 5px;">{{ $exam->title }}</h2><p style="color:var(--muted);">{{ $exam->subject->name }} · {{ $exam->teacher->user->name }}</p></div>
                <strong style="color:{{ $participant->finished_at ? 'var(--success)' : ($isLive ? 'var(--primary)' : 'var(--muted)') }};">{{ $status }}</strong>
            </div>
            <div class="grid two" style="margin-top:18px;">
                <div><p style="color:var(--muted);font-size:12px;">Tanggal & Mulai</p><strong>{{ $exam->start_time->format('d M Y, H:i') }} WIB</strong></div>
                <div><p style="color:var(--muted);font-size:12px;">Berakhir</p><strong>{{ $exam->end_time->format('d M Y, H:i') }} WIB</strong></div>
                <div><p style="color:var(--muted);font-size:12px;">Durasi</p><strong>{{ $exam->duration }} menit</strong></div>
                <div><p style="color:var(--muted);font-size:12px;">Jumlah Soal</p><strong>{{ $exam->question_count ?? 0 }} butir</strong></div>
            </div>

            @if($participant->finished_at)
                <div class="alert success"><i class="fas fa-circle-check"></i> Jawaban telah tersimpan. Nilai akhir akan diumumkan oleh guru.</div>
            @elseif($participant->is_locked)
                <div class="alert error"><i class="fas fa-lock"></i> Sesi dikunci karena pelanggaran: {{ $participant->locked_reason }}. Hubungi guru/pengawas.</div>
            @elseif($isUpcoming)
                <div class="alert" style="background:#fff7ed;color:#9a3412;"><i class="fas fa-clock"></i> Ujian belum dimulai. Silakan kembali pada <strong>{{ $exam->start_time->format('d M Y, H:i') }} WIB</strong>.</div>
            @elseif($isExpired)
                <div class="alert error"><i class="fas fa-calendar-xmark"></i> Jadwal ujian sudah berakhir pada {{ $exam->end_time->format('d M Y, H:i') }} WIB.</div>
            @else
                @if($isLate)
                    <div class="alert" style="background:#fff7ed;color:#9a3412;"><i class="fas fa-person-walking-luggage"></i> Anda masuk terlambat sekitar <strong>{{ $lateMinutes }} menit</strong>. Waktu tetap mengikuti batas akhir ujian.</div>
                @endif
                <div style="margin-top:18px;padding:16px;border:2px solid var(--border);border-radius:10px;background:var(--bg);">
                    <strong><i class="fas fa-shield-halved" style="color:var(--primary);"></i> Peraturan Ujian</strong>
                    <ul style="margin:8px 0 0 20px;color:var(--muted);font-size:13px;line-height:1.7;"><li>Membuka tab/jendela lain akan mengunci sesi.</li><li>Jawaban tersimpan otomatis.</li><li>Gunakan token yang diberikan guru/pengawas.</li></ul>
                </div>
                <form method="POST" action="{{ route('student.exams.start', $participant) }}" style="display:flex;gap:10px;align-items:end;margin-top:18px;flex-wrap:wrap;">
                    @csrf
                    <label style="flex:1;min-width:220px;">Token Ujian<input name="token" placeholder="Masukkan token" maxlength="5" required style="text-transform:uppercase;font-weight:800;letter-spacing:4px;"></label>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-play"></i>&nbsp; {{ $participant->started_at ? 'Lanjutkan Ujian' : 'Masuk Ujian' }}</button>
                </form>
            @endif
        </article>
    @empty
        <article class="card" style="text-align:center;padding:50px;"><i class="fas fa-clipboard-list" style="font-size:48px;color:var(--muted);margin-bottom:15px;"></i><h2>Belum Ada Ujian</h2><p>Belum ada ujian yang ditugaskan untuk akun ini.</p></article>
    @endforelse
</x-layouts.student>
