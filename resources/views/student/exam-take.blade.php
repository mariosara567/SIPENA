<x-layouts.student :title="$exam->title.' - My Asssesmen'" :fullscreen="true">
    <style>
        body { background: #f8fafc; margin: 0; padding: 0; }
        .exam-secure { -webkit-user-select: none; user-select: none; -webkit-touch-callout: none; }
        .tka-header {
            background: #0284c7; color: #fff; padding: 12px 24px;
            display: flex; justify-content: space-between; align-items: center;
            height: 64px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
        }
        .tka-header-left { display: flex; align-items: center; gap: 16px; font-weight: 600; font-size: 15px; }
        .tka-header-right { display: flex; align-items: center; gap: 16px; }
        
        .tka-btn-nav { background: #2563eb; color: #fff; border: 1px solid #3b82f6; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .tka-btn-nav:hover { background: #1d4ed8; }
        .tka-timer { background: #eab308; color: #000; padding: 8px 16px; border-radius: 6px; font-weight: 800; display: flex; align-items: center; gap: 8px; font-size: 15px; }

        .tka-subheader {
            background: #fff; padding: 12px 24px; border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
            position: fixed; top: 64px; left: 0; right: 0; z-index: 40;
        }
        .tka-badge { background: #eff6ff; color: #1d4ed8; padding: 6px 16px; border-radius: 99px; font-weight: 700; font-size: 13px; }
        .tka-font-ctrl { color: #64748b; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
        .tka-font-ctrl button { background: none; border: none; font-weight: 700; color: #1d4ed8; cursor: pointer; font-size: 14px; }

        .tka-content {
            padding: 40px 24px; max-width: 1100px; margin: 120px auto 100px;
            font-size: var(--question-font-size, 15px); transition: font-size 0.2s;
        }
        
        .exam-question { display: none; }
        .exam-question.active { display: block; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        .tka-image-note { font-weight: 700; margin-bottom: 12px; color: #0f172a; }
        .tka-question-img { max-width: 100%; max-height: 400px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 24px; }
        .tka-question-text { line-height: 1.7; color: #334155; margin-bottom: 32px; white-space: pre-line; }

        .tka-option {
            display: flex; align-items: flex-start; gap: 16px;
            padding: 16px 20px; border: 1px solid #e2e8f0; border-radius: 12px;
            cursor: pointer; margin-bottom: 12px; background: #fff; transition: all 0.2s;
        }
        .tka-option:hover { border-color: #94a3b8; }
        .tka-option:has(input:checked) { border-color: #0284c7; background: #f0f9ff; }
        .tka-option input { display: none; }
        
        .tka-option-letter {
            width: 32px; height: 32px; flex: 0 0 32px;
            border: 1px solid #cbd5e1; border-radius: 8px;
            display: grid; place-items: center;
            font-weight: 700; color: #64748b; background: #f8fafc;
        }
        .tka-option:has(input:checked) .tka-option-letter {
            background: #0284c7; border-color: #0284c7; color: #fff;
        }
        .tka-option-text { margin-top: 5px; color: #334155; line-height: 1.5; font-weight: 500; }
        
        .tka-footer {
            background: #fff; border-top: 1px solid #e2e8f0; padding: 16px 24px;
            display: flex; justify-content: space-between; align-items: center;
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 40; box-shadow: 0 -2px 10px rgba(0,0,0,0.02);
        }
        
        .tka-btn { border: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 15px; display: inline-flex; align-items: center; gap: 8px; transition: opacity 0.2s; }
        .tka-btn:hover { opacity: 0.9; }
        .tka-btn-prev { background: #64748b; color: #fff; }
        .tka-btn-doubt { background: #eab308; color: #422006; padding: 10px 24px; border-radius: 6px; font-weight: 700; }
        .tka-btn-next { background: #0284c7; color: #fff; }
        .tka-btn-finish { background: #22c55e; color: #fff; }

        /* Navigation Modal */
        .tka-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.6);
            z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px;
        }
        .tka-overlay.open { display: flex; }
        .tka-modal {
            background: #f8fafc; border-radius: 12px; width: 100%; max-width: 680px;
            overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
        }
        .tka-modal-header {
            background: #0284c7; color: #fff; padding: 16px 24px;
            display: flex; justify-content: space-between; align-items: center; font-weight: 700;
        }
        .tka-modal-close { background: none; border: none; color: #fff; font-size: 24px; cursor: pointer; }
        .tka-modal-body { padding: 24px; }
        
        .tka-legend { display: flex; gap: 16px; margin-bottom: 24px; font-size: 13px; font-weight: 600; color: #475569; }
        .tka-legend div { display: flex; align-items: center; gap: 8px; }
        .tka-box { width: 16px; height: 16px; border-radius: 4px; border: 1px solid #cbd5e1; background: #fff; }
        .tka-box.blue { background: #0284c7; border-color: #0284c7; }
        .tka-box.yellow { background: #eab308; border-color: #ca8a04; }
        
        .tka-grid { display: grid; grid-template-columns: repeat(10, 1fr); gap: 10px; }
        .tka-num-btn {
            aspect-ratio: 1; border: 1px solid #cbd5e1; border-radius: 8px;
            background: #fff; font-weight: 700; cursor: pointer;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            font-size: 16px; color: #334155; position: relative; transition: all 0.1s;
        }
        .tka-num-btn:hover { border-color: #0284c7; }
        .tka-num-btn.answered { background: #0284c7; border-color: #0284c7; color: #fff; }
        .tka-num-btn.doubted { background: #eab308; border-color: #ca8a04; color: #422006; }
        .tka-num-btn .opt-ans { font-size: 11px; position: absolute; bottom: 4px; right: 6px; font-weight: 800; }
        
        /* Modals (Finish, Lock) */
        .tka-alert-modal { background: #fff; border-radius: 12px; width: 100%; max-width: 480px; text-align: center; overflow: hidden; }
        .tka-alert-body { padding: 32px 24px; }
        .tka-security-modal { max-width: 460px; }
        
        @media(max-width:700px){
            .tka-header { height:64px; padding:8px 12px; gap:8px; }
            .tka-header-left { min-width:0; flex:1; gap:8px; font-size:12px; }
            .tka-header-left span:first-child { display:none; }
            .tka-header-left span:nth-child(2) { display:none; }
            .tka-header-left span:last-child { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
            .tka-header-right { gap:6px; }
            .tka-btn-nav { width:42px; height:42px; justify-content:center; padding:0; font-size:0; }
            .tka-btn-nav i { font-size:16px; }
            .tka-timer { padding:8px 10px; font-size:13px; }
            .tka-timer i { display:none; }
            .tka-subheader { top:64px; padding:9px 12px; min-height:48px; }
            .tka-badge { padding:5px 9px; font-size:11px; }
            .tka-font-ctrl { gap:7px; font-size:11px; }
            .tka-content { margin:112px auto 132px; padding:20px 14px; font-size:var(--question-font-size, 15px); }
            .tka-question-text { margin-bottom:22px; line-height:1.65; }
            .tka-question-img { max-height:280px; margin-bottom:18px; }
            .tka-option { gap:12px; padding:13px; margin-bottom:10px; border-radius:10px; }
            .tka-option-letter { width:28px; height:28px; flex-basis:28px; border-radius:7px; }
            .tka-option-text { margin-top:3px; font-size:14px; }
            .tka-grid { grid-template-columns: repeat(5, 1fr); }
            .tka-footer { padding:10px 12px; gap:8px; display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .tka-footer .tka-btn { width:100%; padding:10px 8px; font-size:12px; justify-content:center; }
            .tka-btn-doubt { grid-column:1 / -1; grid-row:2; justify-content:center; padding:9px 12px; font-size:12px; }
            .tka-overlay { padding:12px; align-items:flex-end; }
            .tka-modal, .tka-alert-modal { max-height:calc(100vh - 24px); overflow:auto; border-radius:12px; }
            .tka-modal-header, .tka-modal-body { padding:14px; }
            .tka-legend { flex-wrap:wrap; gap:9px; margin-bottom:16px; font-size:11px; }
            .tka-alert-body { padding:24px 18px; }
        }
    </style>

    <form id="submit-form" method="POST" action="{{ route('student.exams.submit', $participant) }}">@csrf</form>

    <div class="tka-header">
        <div class="tka-header-left">
            <span style="font-family: 'Brush Script MT', cursive; font-size: 24px; margin-right: 12px;">My Asssesmen</span>
            <span style="font-weight:400; opacity:0.8;">|</span>
            <span>Mata Ujian: {{ $exam->subject->name }}</span>
        </div>
        <div class="tka-header-right">
            <button type="button" class="tka-btn-nav" id="open-nav"><i class="fas fa-grip"></i> Daftar Soal</button>
            <div class="tka-timer"><i class="fas fa-stopwatch"></i> <span id="timer">00:00:00</span></div>
        </div>
    </div>

    <div class="tka-subheader">
        <div class="tka-badge" id="question-badge">SOAL NOMOR 1</div>
        <div class="tka-font-ctrl">
            Ukuran font soal:
            <button type="button" onclick="changeFontSize(-2)">A-</button>
            <button type="button" onclick="changeFontSize(0)">A</button>
            <button type="button" onclick="changeFontSize(2)">A+</button>
        </div>
    </div>

    <main class="tka-content exam-secure" id="question-container" style="--question-font-size: 15px;">
        @foreach($exam->questions as $question)
            @php($saved = $answers->get($question->id)?->answer)
            <div class="exam-question {{ $loop->first ? 'active' : '' }}" data-index="{{ $loop->index }}">
                
                @if($question->image_path)
                    <p class="tka-image-note">Perhatikan gambar di bawah ini!</p>
                    <img src="{{ asset('storage/'.$question->image_path) }}" class="tka-question-img" alt="Gambar Soal">
                @endif
                
                <div class="tka-question-text">{{ $question->question }}</div>
                
                <div class="tka-options">
                    @foreach(['A','B','C','D','E'] as $option)
                        @php($content = $question->{'option_'.strtolower($option)} ?? null)
                        @if($content)
                            <label class="tka-option">
                                <input type="radio" name="q{{ $question->id }}" value="{{ $option }}" data-qid="{{ $question->id }}" data-idx="{{ $loop->parent->index }}" @checked($saved === $option)>
                                <div class="tka-option-letter">{{ $option }}</div>
                                <div class="tka-option-text">{{ $content }}</div>
                            </label>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </main>

    <footer class="tka-footer">
        <button type="button" class="tka-btn tka-btn-prev" id="btn-prev"><i class="fas fa-chevron-left"></i> Soal Sebelumnya</button>
        <label class="tka-btn-doubt" style="cursor:pointer; display:inline-flex; align-items:center;">
            <input type="checkbox" id="doubt-check" style="margin-right:8px; width:16px; height:16px;"> Ragu - Ragu
        </label>
        <button type="button" class="tka-btn tka-btn-next" id="btn-next">Soal Berikutnya <i class="fas fa-chevron-right"></i></button>
    </footer>

    <!-- Nav Modal -->
    <div class="tka-overlay" id="nav-modal">
        <div class="tka-modal">
            <div class="tka-modal-header">
                <span><i class="fas fa-grip"></i> Navigasi Lembar Jawaban</span>
                <button class="tka-modal-close" data-close>&times;</button>
            </div>
            <div class="tka-modal-body">
                <div class="tka-legend">
                    <div><div class="tka-box blue"></div> Sudah Dijawab</div>
                    <div><div class="tka-box yellow"></div> Ragu-Ragu</div>
                    <div><div class="tka-box"></div> Belum Dijawab</div>
                </div>
                <div class="tka-grid" id="nav-grid">
                    @foreach($exam->questions as $question)
                        @php($saved = $answers->get($question->id)?->answer)
                        <button type="button" class="tka-num-btn {{ $saved ? 'answered' : '' }}" data-jump="{{ $loop->index }}">
                            {{ $loop->iteration }}
                            <span class="opt-ans" id="opt-ans-{{ $loop->index }}">{{ $saved ?? '' }}</span>
                        </button>
                    @endforeach
                </div>
                <div style="text-align:right; margin-top:24px;">
                    <button class="tka-btn" style="background:#475569; color:#fff;" data-close>Kembali Ke Soal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Finish Modal -->
    <div class="tka-overlay" id="finish-modal">
        <div class="tka-alert-modal">
            <div class="tka-alert-body">
                <i class="fas fa-triangle-exclamation" style="font-size:56px; color:#f59e0b; margin-bottom:16px;"></i>
                <h2 style="margin:0 0 8px; font-size:20px; color:#0f172a;">Konfirmasi Mengakhiri Ujian</h2>
                <p style="color:#64748b; font-size:14px; margin-bottom:24px;">Apakah Anda yakin ingin menyelesaikan lembar ujian ini?<br>Setelah dikirim, jawaban Anda tidak dapat diubah kembali.</p>
                
                <label style="display:flex; align-items:flex-start; text-align:left; gap:12px; padding:16px; border:1px solid #e2e8f0; border-radius:12px; margin-bottom:24px; background:#f8fafc; cursor:pointer;">
                    <input type="checkbox" id="finish-check" style="width:20px; min-width:20px; height:20px; margin-top:2px;"> 
                    <span style="font-size:13px; color:#334155; line-height:1.5;">Saya menyatakan secara sadar telah memeriksa kembali semua jawaban saya dari nomor 1 sampai {{ $exam->questions->count() }}.</span>
                </label>
                
                <div style="display:flex; gap:12px;">
                    <button class="tka-btn" style="flex:1; background:#cbd5e1; color:#334155; justify-content:center;" data-close>Batal</button>
                    <button class="tka-btn" style="flex:1; background:#cbd5e1; color:#fff; justify-content:center; cursor:not-allowed;" id="confirm-finish" disabled>Ya, Selesai</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lock Modal -->
    <div class="tka-overlay" id="lock-modal">
        <div class="tka-alert-modal">
            <div class="tka-alert-body">
                <i class="fas fa-lock" style="font-size:56px; color:#dc2626; margin-bottom:16px;"></i>
                <h2 style="margin:0 0 8px; font-size:20px; color:#0f172a;">Sesi Ujian Dikunci</h2>
                <p style="color:#64748b; font-size:14px; margin-bottom:24px;">Sistem mendeteksi Anda meninggalkan halaman ujian.<br>Hubungi pengawas untuk membuka kunci agar dapat melanjutkan.</p>
                <a href="{{ route('student.exams.index') }}" class="tka-btn tka-btn-next" style="width:100%; justify-content:center; text-decoration:none;">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <!-- Secure mode is required before questions can be accessed. -->
    <div class="tka-overlay open" id="security-modal">
        <div class="tka-alert-modal tka-security-modal">
            <div class="tka-alert-body">
                <i class="fas fa-shield-halved" style="font-size:56px; color:#0284c7; margin-bottom:16px;"></i>
                <h2 style="margin:0 0 8px; font-size:20px; color:#0f172a;">Aktifkan Mode Aman</h2>
                <p style="color:#64748b; font-size:14px; line-height:1.6; margin-bottom:24px;">Ujian dijalankan dalam layar penuh. Jangan berpindah tab, aplikasi, atau keluar dari layar penuh karena sesi akan dikunci.</p>
                <button type="button" class="tka-btn tka-btn-next" id="start-secure" style="width:100%; justify-content:center;">Mulai Ujian</button>
            </div>
        </div>
    </div>

    <script>
        // Core Variables
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const questions = [...document.querySelectorAll('.exam-question')];
        const jumps = [...document.querySelectorAll('.tka-num-btn')];
        let current = 0;
        let examActive = true;
        let reporting = false;
        let secureMode = false;
        const doubts = new Set();
        let currentFontSize = 15;
        const maxQuestions = questions.length;

        const reportViolation = async (reason) => {
            if (!examActive || reporting) return;
            reporting = true;
            examActive = false;
            try {
                await fetch(@json(route('student.exams.violation', $participant)), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ reason }),
                    keepalive: true,
                });
            } finally {
                document.querySelectorAll('.tka-overlay').forEach(m => m.classList.remove('open'));
                document.getElementById('lock-modal').classList.add('open');
                reporting = false;
            }
        };

        const enterSecureMode = async () => {
            try {
                if (document.documentElement.requestFullscreen) {
                    await document.documentElement.requestFullscreen();
                }
                secureMode = true;
                document.getElementById('security-modal').classList.remove('open');
            } catch (e) {
                alert('Browser menolak layar penuh. Izinkan mode layar penuh untuk memulai ujian.');
            }
        };

        document.getElementById('start-secure').onclick = enterSecureMode;

        // Font Controls
        window.changeFontSize = (step) => {
            if (step === 0) currentFontSize = 15;
            else currentFontSize = Math.max(12, Math.min(24, currentFontSize + step));
            document.getElementById('question-container').style.setProperty('--question-font-size', currentFontSize + 'px');
        };

        // Navigation
        const show = (idx) => {
            current = Math.max(0, Math.min(idx, maxQuestions - 1));
            questions.forEach((q, i) => q.classList.toggle('active', i === current));
            
            document.getElementById('question-badge').textContent = 'SOAL NOMOR ' + (current + 1);
            
            const prev = document.getElementById('btn-prev');
            if (current === 0) { prev.style.visibility = 'hidden'; } else { prev.style.visibility = 'visible'; }
            
            const next = document.getElementById('btn-next');
            if (current === maxQuestions - 1) {
                next.innerHTML = 'Selesai Ujian <i class="far fa-check-circle"></i>';
                next.className = 'tka-btn tka-btn-finish';
            } else {
                next.innerHTML = 'Soal Berikutnya <i class="fas fa-chevron-right"></i>';
                next.className = 'tka-btn tka-btn-next';
            }
            
            document.getElementById('doubt-check').checked = doubts.has(current);
        };
        show(0);

        // Events
        document.getElementById('btn-prev').onclick = () => show(current - 1);
        document.getElementById('btn-next').onclick = () => {
            if (current === maxQuestions - 1) {
                document.getElementById('finish-modal').classList.add('open');
            } else {
                show(current + 1);
            }
        };
        
        document.getElementById('doubt-check').onchange = (e) => {
            if(e.target.checked) {
                doubts.add(current);
                jumps[current].classList.add('doubted');
            } else {
                doubts.delete(current);
                jumps[current].classList.remove('doubted');
            }
        };

        // Modal triggers
        document.getElementById('open-nav').onclick = () => document.getElementById('nav-modal').classList.add('open');
        document.querySelectorAll('[data-close]').forEach(btn => {
            btn.onclick = () => document.querySelectorAll('.tka-overlay').forEach(m => m.classList.remove('open'));
        });
        jumps.forEach(btn => {
            btn.onclick = () => {
                show(+btn.dataset.jump);
                document.getElementById('nav-modal').classList.remove('open');
            }
        });

        // Submit logic
        const check = document.getElementById('finish-check');
        const confirmBtn = document.getElementById('confirm-finish');
        check.onchange = () => {
            if (check.checked) {
                confirmBtn.disabled = false;
                confirmBtn.style.background = '#0284c7';
                confirmBtn.style.cursor = 'pointer';
            } else {
                confirmBtn.disabled = true;
                confirmBtn.style.background = '#cbd5e1';
                confirmBtn.style.cursor = 'not-allowed';
            }
        };

        const postAnswer = async (questionId, answer) => {
            const res = await fetch(@json(route('student.exams.answers.save', $participant)), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ question_id: questionId, answer })
            });
            if (!res.ok) throw new Error('Failed');
        };

        const pendingKey = 'my_asssesmen:pending_answers:' + @json($participant->id);
        const readPending = () => { try { return JSON.parse(localStorage.getItem(pendingKey) || '{}') } catch { return {} } };
        const writePending = data => localStorage.setItem(pendingKey, JSON.stringify(data));

        document.querySelectorAll('input[type="radio"]').forEach(input => {
            input.onchange = async () => {
                const qid = +input.dataset.qid;
                const idx = +input.dataset.idx;
                const ans = input.value;
                
                // update grid UI instantly
                jumps[idx].classList.add('answered');
                document.getElementById('opt-ans-' + idx).textContent = ans;
                
                try {
                    await postAnswer(qid, ans);
                    const p = readPending(); delete p[qid]; writePending(p);
                } catch(e) {
                    const p = readPending(); p[qid] = ans; writePending(p);
                }
            };
        });

        const flushPending = async () => {
            const p = readPending();
            const entries = Object.entries(p);
            if (!entries.length) return true;
            for (const [qid, ans] of entries) {
                try { 
                    await postAnswer(+qid, ans); 
                    const latest = readPending(); delete latest[qid]; writePending(latest);
                } catch(e) {
                    return false;
                }
            }
            return true;
        };

        window.addEventListener('online', flushPending);

        confirmBtn.onclick = async () => {
            if(!check.checked) return;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = 'Menyimpan...';
            const ok = await flushPending();
            if (!ok) {
                alert('Koneksi internet bermasalah. Pastikan jawaban Anda terkirim dengan merefresh halaman (jangan keluar dari browser).');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Ya, Selesai';
                return;
            }
            examActive = false;
            document.getElementById('submit-form').submit();
        };

        // Timer Logic
        const endTimeStr = @json($endTimeIso);
        const end = new Date(endTimeStr).getTime();
        const timerEl = document.getElementById('timer');
        
        const tick = async () => {
            const now = new Date().getTime();
            const d = end - now;
            if (d <= 0) {
                timerEl.textContent = "00:00:00";
                const ok = await flushPending();
                if (ok) {
                    examActive = false;
                    document.getElementById('submit-form').submit();
                }
                return;
            }
            const totalS = Math.floor(d / 1000);
            const h = Math.floor(totalS / 3600);
            const m = Math.floor((totalS % 3600) / 60);
            const s = Math.floor(totalS % 60);
            timerEl.textContent = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
        };
        setInterval(tick, 1000);
        tick();

        // Anti-cheat: browser cannot block a device-level screenshot, but it can
        // block common browser actions and lock the session when they are attempted.
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('dragstart', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());
        document.addEventListener('paste', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            const key = e.key.toLowerCase();
            const blocked = (e.ctrlKey || e.metaKey) && ['c', 'x', 'v', 'p', 's', 'u', 't', 'n', 'w', 'l'].includes(key);
            if (blocked || e.key === 'F12') {
                e.preventDefault();
                return;
            }
            if (e.key === 'PrintScreen') {
                e.preventDefault();
                reportViolation('Mencoba mengambil tangkapan layar');
            }
        });

        document.addEventListener('visibilitychange', async () => {
            if (document.hidden) reportViolation('Meninggalkan halaman ujian / membuka tab lain');
        });

        document.addEventListener('fullscreenchange', () => {
            if (secureMode && !document.fullscreenElement) reportViolation('Keluar dari mode layar penuh');
        });
    </script>
</x-layouts.student>
