<x-layouts.app :title="'Ujian Saya - My Asssesmen'">
    <section class="card" style="display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap;">
        <div>
            <h1 style="margin:0 0 8px;">Portal Ujian Siswa</h1>
            <p>Periksa biodata, paket ujian, dan peraturan sebelum memasukkan token.</p>
        </div>
        <span class="badge" style="background:#dcfce7;color:#166534;padding:8px 13px;border-radius:999px;font-weight:700;">
            <i class="fas fa-circle-check"></i> Siswa Aktif
        </span>
    </section>

    <section class="grid two" style="align-items:start;">
        <aside class="card" style="text-align:center;position:sticky;top:0;">
            <div style="width:92px;height:92px;margin:0 auto 15px;border-radius:50%;display:grid;place-items:center;background:rgba(15,118,110,.12);color:var(--primary);font-size:42px;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h2 style="margin:0 0 6px;">{{ auth()->user()->name }}</h2>
            <p>NISN: {{ auth()->user()->student->nis }}</p>
            <p>Kelas: {{ auth()->user()->student->schoolClass?->display_name ?? '-' }}</p>
            <div style="border-top:2px solid var(--border);margin-top:18px;padding-top:18px;text-align:left;">
                <h3 style="margin:0 0 10px;">Verifikasi Data</h3>
                <p>Pastikan profil dan detail ujian sudah sesuai. Jika ada kesalahan, hubungi operator sekolah sebelum ujian dimulai.</p>
            </div>
        </aside>

        <div>
            @forelse($participants as $participant)
                @php($exam = $participant->exam)
                <article class="card" style="border-top:5px solid {{ $participant->finished_at ? 'var(--success)' : 'var(--primary)' }};">
                    <div class="card-header">
                        <div>
                            <h2 style="margin:0;">{{ $exam->title }}</h2>
                            <p>{{ $exam->subject->name }} · {{ $exam->teacher->user->name }}</p>
                        </div>
                        <strong style="color:{{ $participant->finished_at ? 'var(--success)' : 'var(--primary)' }};">
                            {{ $participant->finished_at ? 'Selesai' : ($participant->started_at ? 'Sedang Berjalan' : 'Belum Mulai') }}
                        </strong>
                    </div>

                    <div class="grid two">
                        <div><p>Durasi</p><strong>{{ $exam->duration }} menit</strong></div>
                        <div><p>Jumlah Soal</p><strong>{{ $exam->question_count ?? 0 }} butir</strong></div>
                        <div><p>Mulai</p><strong>{{ $exam->start_time->format('d M Y, H:i') }}</strong></div>
                        <div><p>Selesai</p><strong>{{ $exam->end_time->format('d M Y, H:i') }}</strong></div>
                    </div>

                    @if($participant->finished_at)
                        <div class="alert success" style="margin:20px 0 0;">
                            <i class="fas fa-circle-check"></i>
                            <span>Jawaban telah tersimpan. Nilai akhir akan diumumkan oleh guru mata pelajaran.</span>
                        </div>
                    @elseif($participant->is_locked)
                        <div class="alert error" style="margin:20px 0 0;">
                            <i class="fas fa-lock"></i>
                            <span>Sesi dikunci karena pelanggaran: {{ $participant->locked_reason }}. Hubungi guru/pengawas.</span>
                        </div>
                    @else
                        <div style="margin-top:20px;padding:18px;border:2px solid var(--border);border-radius:10px;background:var(--bg);">
                            <h3 style="margin:0 0 10px;"><i class="fas fa-shield-halved" style="color:var(--primary);"></i> Peraturan Ujian</h3>
                            <ul style="margin:0;padding-left:20px;color:var(--text-muted);font-size:14px;line-height:1.7;">
                                <li>Membuka tab atau jendela lain akan mengunci sesi ujian.</li>
                                <li>Jawaban tersimpan otomatis setiap kali pilihan dijawab.</li>
                                <li>Gunakan jaringan sekolah dan ikuti arahan guru/pengawas.</li>
                            </ul>
                        </div>
                        <form method="POST" action="{{ route('student.exams.start', $participant) }}" style="display:flex;gap:10px;align-items:end;margin-top:18px;flex-wrap:wrap;">
                            @csrf
                            <label style="margin:0;flex:1;min-width:220px;">Token Ujian
                                <input name="token" placeholder="Masukkan token" required style="text-transform:uppercase;font-weight:700;letter-spacing:2px;">
                            </label>
                            <button class="btn btn-primary" type="submit">
                                {{ $participant->started_at ? 'Lanjutkan Ujian' : 'Mulai Ujian' }} <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    @endif
                </article>
            @empty
                <article class="card" style="text-align:center;padding:50px;">
                    <i class="fas fa-clipboard-list" style="font-size:48px;color:var(--text-muted);margin-bottom:15px;"></i>
                    <h2>Belum Ada Ujian</h2>
                    <p>Belum ada mata pelajaran yang ditugaskan untuk akun ini.</p>
                </article>
            @endforelse
        </div>
    </section>
</x-layouts.app>
