<x-layouts.student :title="'Portal Ujian - My Asssesmen'">
    <style>
        .portal-layout {
            display: flex;
            gap: 24px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .portal-sidebar {
            flex: 0 0 320px;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
            text-align: center;
        }
        .portal-sidebar-header {
            background: var(--primary);
            height: 100px;
            width: 100%;
        }
        .portal-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #fff;
            margin-top: -50px;
            background: #f1f5f9;
            object-fit: cover;
        }
        .portal-content {
            flex: 1;
            min-width: 300px;
        }
        .exam-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            display: block;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }
        .exam-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
    </style>

    <div class="page-warning" style="background:#fffbeb; border:1px solid #fde68a; padding:16px 20px; border-radius:8px; margin-bottom:24px; display:flex; align-items:center; gap:12px;">
        <i class="fas fa-triangle-exclamation" style="color:#d97706; font-size:24px;"></i>
        <div>
            <h4 style="margin:0 0 4px; color:#b45309; font-size:15px;">Harap Verifikasi Data Anda</h4>
            <p style="margin:0; color:#b45309; font-size:13px;">Pastikan informasi profil di sebelah kiri dan detail ujian di sebelah kanan sudah sesuai sebelum memilih ujian.</p>
        </div>
    </div>

    <div class="portal-layout">
        {{-- Kolom Kiri: Profil --}}
        <aside class="portal-sidebar">
            <div class="portal-sidebar-header"></div>
            @php
                $gender = auth()->user()->gender ?? 'Laki-laki';
                $avatarSrc = $gender === 'Perempuan' ? asset('images/girl.png') : asset('images/boy.png');
            @endphp
            <img src="{{ $avatarSrc }}" alt="Avatar" class="portal-avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0284c7&color=fff'">
            
            <div style="padding: 16px 20px 24px;">
                <div style="display:inline-block; padding:4px 12px; background:#dcfce7; color:#166534; font-size:11px; font-weight:700; border-radius:99px; margin-bottom:12px;">
                    SISWA AKTIF
                </div>
                <h2 style="margin:0 0 4px; font-size:18px; color:var(--text);">{{ auth()->user()->name }}</h2>
                <p style="margin:0 0 4px; color:var(--text-muted); font-size:14px;">NISN : {{ auth()->user()->student->nisn }}</p>
                <p style="margin:0 0 24px; color:var(--text-muted); font-size:14px;">Kelas : {{ auth()->user()->student->schoolClass?->display_name ?? '-' }}</p>
                
                <hr style="border:none; border-top:1px solid var(--border); margin-bottom:16px;">
                
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-portal').submit();" style="color:var(--text-muted); text-decoration:none; font-size:13px; display:inline-block;">
                    Bukan akun Anda? <strong style="color:var(--primary);">Keluar Aplikasi</strong>
                </a>
                <form id="logout-form-portal" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </aside>

        {{-- Kolom Kanan: Daftar Ujian --}}
        <main class="portal-content">
            <h1 style="margin:0 0 16px; font-size:20px;">Daftar Ujian Hari Ini</h1>
            
            @forelse($participants as $participant)
                @php
                    $exam = $participant->exam;
                    $now = now();
                    $start = $exam->start_time ?? $now->copy()->addDay();
                    $end = $exam->end_time ?? $now->copy()->addDay();
                    $isLive = $now->gte($start) && $now->lt($end);
                    $isExpired = $now->gte($end);
                    $status = $participant->is_locked ? 'Terkunci' : ($isLive ? 'Sedang Berlangsung' : ($isExpired ? 'Berakhir' : 'Belum Dimulai'));
                    $statusColor = $participant->is_locked ? '#ef4444' : ($isLive ? 'var(--primary)' : 'var(--text-muted)');
                @endphp
                <a href="{{ route('student.exams.confirm', $participant) }}" class="exam-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                        <div>
                            <h3 style="margin:0 0 4px; font-size:18px;">{{ $exam->title }}</h3>
                            <p style="margin:0; color:var(--text-muted); font-size:13px;">{{ $exam->subject->name }} · {{ $exam->teacher->user->name }}</p>
                        </div>
                        <strong style="color:{{ $statusColor }}; font-size:14px;">{{ $status }}</strong>
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px;">
                        <div>
                            <p style="margin:0; color:var(--text-muted); font-size:12px;">Tanggal & Mulai</p>
                            <strong style="font-size:14px;">{{ $start->format('d M Y, H:i') }} WIB</strong>
                        </div>
                        <div>
                            <p style="margin:0; color:var(--text-muted); font-size:12px;">Durasi</p>
                            <strong style="font-size:14px;">{{ $exam->duration }} Menit</strong>
                        </div>
                    </div>
                </a>
            @empty
                <div style="background:#fff; border:1px solid var(--border); border-radius:12px; padding:40px; text-align:center;">
                    <i class="fas fa-calendar-check" style="font-size:48px; color:#cbd5e1; margin-bottom:16px;"></i>
                    <h3 style="margin:0 0 8px;">Tidak Ada Ujian Aktif</h3>
                    <p style="margin:0; color:var(--text-muted);">Belum ada jadwal ujian yang ditugaskan untuk Anda hari ini, atau Anda sudah menyelesaikan seluruh ujian.</p>
                </div>
            @endforelse
        </main>
    </div>
</x-layouts.student>
