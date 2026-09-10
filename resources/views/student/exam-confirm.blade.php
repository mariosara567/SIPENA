<x-layouts.student :title="'Verifikasi Ujian - My Asssesmen'" :fullscreen="true">
    <style>
        .confirm-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }
        .confirm-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px 32px;
            width: 100%;
            max-width: 750px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .back-link {
            position: absolute;
            top: -36px;
            left: 0;
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            transition: opacity 0.2s;
        }
        .back-link:hover {
            opacity: 0.8;
        }

        .rules-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
        }
        .rules-box ul {
            margin: 10px 0 0 20px;
            padding: 0;
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.5;
        }
        .rules-box ul li { margin-bottom: 6px; }
        .token-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 6px;
            font-weight: 800;
            text-align: center;
            background: #f8fafc;
            height: 52px;
        }
        .token-input:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }
        
        @media (max-width: 600px) {
            .confirm-card { padding: 20px; }
            .back-link { top: -30px; }
            .action-row { flex-direction: column; align-items: stretch !important; }
            .action-row > div, .action-row > button { flex: none; width: 100%; }
        }
    </style>

    <div class="confirm-wrapper">
        <div style="position: relative; width: 100%; max-width: 750px;">
            <a href="{{ route('student.exams.index') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ujian
            </a>
            
            <main class="confirm-card">
                <h2 style="margin:0 0 16px; font-size:18px; color:var(--text); text-align:center;">Informasi Paket Ujian</h2>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; background:#f0f9ff; padding:16px; border-radius:12px; border:1px solid #bae6fd;">
                    <div>
                        <p style="margin:0 0 4px; color:var(--text-muted); font-size:12px;">Mata Pelajaran</p>
                        <strong style="font-size:14px; color:var(--primary);">{{ $exam->subject->name }}</strong>
                    </div>
                    <div>
                        <p style="margin:0 0 4px; color:var(--text-muted); font-size:12px;">Jenis Asesmen</p>
                        <strong style="font-size:14px; color:var(--text);">{{ $exam->title }}</strong>
                    </div>
                    <div>
                        <p style="margin:0 0 4px; color:var(--text-muted); font-size:12px;">Durasi Waktu</p>
                        <strong style="font-size:14px; color:var(--text);">{{ $exam->duration }} Menit</strong>
                    </div>
                    <div>
                        <p style="margin:0 0 4px; color:var(--text-muted); font-size:12px;">Jumlah Soal</p>
                        <strong style="font-size:14px; color:var(--text);">{{ $exam->question_count ?? 0 }} Butir Soal</strong>
                    </div>
                </div>

                <div class="rules-box">
                    <strong style="font-size:13px; color:var(--text); display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-file-contract" style="color:#d97706;"></i> TATA TERTIB & PERATURAN UJIAN:
                    </strong>
                    <ul>
                        <li><strong style="color:#ef4444;">Sistem Anti-Kecurangan Aktif:</strong> Jika Anda mencoba membuka tab baru, berpindah jendela, atau meminimalkan browser, sistem akan otomatis mengeluarkan Anda dari ujian.</li>
                        <li>Jika Anda dikeluarkan karena melanggar, token lama Anda akan hangus. Anda wajib melapor ke pengawas untuk mendapatkan token baru yang berbeda agar bisa masuk kembali.</li>
                        <li>Jawaban akan tersimpan secara otomatis setiap kali Anda menekan tombol "Selanjutnya".</li>
                        <li><strong style="color:#10b981;">Koneksi Internet:</strong> Sistem ini berlangsung full online, pastikan koneksi internet Anda stabil selama proses pengerjaan ujian.</li>
                    </ul>
                </div>

                @php
                    $now = now();
                    $start = $exam->start_time ?? $now->copy()->addDay();
                    $end = $exam->end_time ?? $now->copy()->addDay();
                    $isUpcoming = $now->lt($start);
                    $isExpired = $now->gte($end);
                @endphp

                <form method="POST" action="{{ route('student.exams.start', $participant) }}" style="margin-top:20px;">
                    @csrf
                    
                    @if($participant->is_locked)
                        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:16px; font-size:12px; font-weight:600; text-align:center;">
                            Sesi dikunci: {{ $participant->locked_reason }}. Hubungi guru.
                        </div>
                    @elseif($isUpcoming)
                        <div style="background:#fef3c7; color:#92400e; padding:12px; border-radius:8px; margin-bottom:16px; font-size:12px; font-weight:600; text-align:center;">
                            Ujian belum dimulai. Silakan kembali pada {{ $start->format('d M Y, H:i') }} WIB.
                        </div>
                    @elseif($isExpired)
                        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:16px; font-size:12px; font-weight:600; text-align:center;">
                            Jadwal ujian sudah berakhir pada {{ $end->format('d M Y, H:i') }} WIB.
                        </div>
                    @endif

                    @if($errors->has('token'))
                        <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:16px; font-size:12px; font-weight:600; text-align:center;">
                            {{ $errors->first('token') }}
                        </div>
                    @endif

                    <div class="action-row" style="display:flex; gap:16px; align-items:flex-end;">
                        <div style="flex:1;">
                            <label style="display:block; font-size:12px; color:var(--text-muted); margin-bottom:8px; text-align:left; font-weight:600;">Masukkan Token Ujian</label>
                            <input type="text" name="token" class="token-input" placeholder="CONTOH: ABX9U" required maxlength="5" autocomplete="off" {{ ($participant->is_locked || $isUpcoming || $isExpired) ? 'disabled' : 'autofocus' }}>
                        </div>
                        <button type="submit" class="btn" style="background:var(--primary); color:#fff; border:none; padding:0 24px; font-size:15px; font-weight:700; border-radius:8px; cursor:pointer; flex:1; height: 52px; opacity: {{ ($participant->is_locked || $isUpcoming || $isExpired) ? '0.5' : '1' }}" {{ ($participant->is_locked || $isUpcoming || $isExpired) ? 'disabled' : '' }}>
                            Mulai Ujian Sekarang <i class="fas fa-arrow-right" style="margin-left:8px;"></i>
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</x-layouts.student>
