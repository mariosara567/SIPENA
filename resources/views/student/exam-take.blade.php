<x-layouts.student :title="$exam->title.' - My Asssesmen'" :fullscreen="true">
    <style>
        .exam-question{display:none}.exam-question.active{display:block}.exam-option{display:flex;align-items:flex-start;gap:12px;padding:13px 15px;border:2px solid var(--border);border-radius:10px;cursor:pointer;margin-bottom:10px;background:var(--surface)}.exam-option:hover{border-color:var(--primary)}.exam-option:has(input:checked){border-color:var(--primary);background:rgba(15,118,110,.08)}.exam-option input{width:20px;min-height:20px;margin-top:1px}.option-letter{width:28px;height:28px;flex:0 0 28px;border:2px solid var(--border);border-radius:7px;display:grid;place-items:center;font-weight:800;color:var(--primary)}.exam-option:has(input:checked) .option-letter{background:var(--primary);border-color:var(--primary);color:#fff}.exam-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}.exam-modal-layer{display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:1000;align-items:center;justify-content:center;padding:20px}.exam-modal-layer.open{display:flex}.exam-modal{width:min(680px,100%);background:#fff;border-radius:14px;box-shadow:var(--shadow-lg);overflow:hidden}.exam-modal-header{padding:18px 22px;background:var(--primary);color:#fff;display:flex;justify-content:space-between;font-weight:800}.exam-modal-body{padding:22px}.question-grid{display:grid;grid-template-columns:repeat(10,1fr);gap:9px}.question-jump{min-height:50px;border:2px solid var(--border);border-radius:8px;background:#fff;font-weight:700;cursor:pointer}.question-jump.answered{background:var(--primary);border-color:var(--primary);color:#fff}.question-jump.doubted{background:#fbbf24;border-color:#f59e0b;color:#422006}.close-modal{border:0;background:none;color:inherit;font-size:20px;cursor:pointer}.confirm-actions{display:flex;gap:10px}.confirm-actions>*{flex:1}@media(max-width:700px){.question-grid{grid-template-columns:repeat(5,1fr)}.exam-actions .btn{padding:0 12px}}
    </style>

    <section class="card" style="display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;border-top:5px solid var(--primary);">
        <div>
            <h1 style="margin:0 0 6px;">{{ $exam->title }}</h1>
            <p>{{ $exam->subject->name }} · <span id="save-status" style="font-weight:800;color:#047857;">✓ Terhubung</span></p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <button class="btn btn-secondary" type="button" id="open-navigation"><i class="fas fa-grip"></i> Daftar Soal</button>
            <div style="padding:11px 16px;border-radius:9px;background:#fff7d6;color:#92400e;font-weight:800;border:2px solid #fbbf24;">
                <i class="fas fa-stopwatch"></i> <span id="timer">--:--</span>
            </div>
        </div>
    </section>

    <form id="submit-form" method="POST" action="{{ route('student.exams.submit', $participant) }}">@csrf</form>

    <section class="card">
        @foreach($exam->questions as $question)
            @php($saved = $answers->get($question->id)?->answer)
            <article class="exam-question {{ $loop->first ? 'active' : '' }}" data-index="{{ $loop->index }}">
                <div class="card-header">
                    <span style="padding:7px 13px;border-radius:999px;background:rgba(15,118,110,.1);color:var(--primary);font-weight:800;">SOAL NOMOR {{ $loop->iteration }}</span>
                    <span style="color:var(--text-muted);">{{ $loop->iteration }} dari {{ $exam->questions->count() }}</span>
                </div>
                <h2 style="font-size:18px;margin-bottom:12px;">Perhatikan pertanyaan berikut!</h2>
                <div class="question-text" style="font-size:16px;line-height:1.7;white-space:pre-line;margin-bottom:18px;">{{ $question->question }}</div>
                @if($question->image_path)
                    <div style="margin-bottom:22px;text-align:center;"><img src="{{ asset('storage/'.$question->image_path) }}" alt="Gambar soal" style="max-width:100%;max-height:360px;object-fit:contain;border-radius:10px;border:1px solid var(--border);"></div>
                @endif
                @foreach(['A','B','C','D','E'] as $option)
                    @php($content = $question->{'option_'.strtolower($option)} ?? null)
                    @if($content)
                        <label class="exam-option">
                            <input type="radio" name="q{{ $question->id }}" value="{{ $option }}" data-question-id="{{ $question->id }}" @checked($saved === $option)>
                            <span class="option-letter">{{ $option }}</span><span>{{ $content }}</span>
                        </label>
                    @endif
                @endforeach
            </article>
        @endforeach
    </section>

    <section class="card exam-actions">
        <button type="button" class="btn btn-secondary" id="previous"><i class="fas fa-chevron-left"></i> Soal Sebelumnya</button>
        <button type="button" class="btn" id="doubt" style="background:#fbbf24;color:#422006;"><i class="far fa-square"></i> Ragu-Ragu</button>
        <button type="button" class="btn btn-primary" id="next">Soal Berikutnya <i class="fas fa-chevron-right"></i></button>
    </section>

    <div class="exam-modal-layer" id="navigation-modal"><div class="exam-modal"><div class="exam-modal-header"><span><i class="fas fa-grip"></i> Navigasi Lembar Jawaban</span><button class="close-modal" data-close>×</button></div><div class="exam-modal-body"><p style="margin-bottom:16px;">Hijau: sudah dijawab · Kuning: ragu-ragu · Putih: belum dijawab</p><div class="question-grid">@foreach($exam->questions as $question)<button type="button" class="question-jump {{ $answers->get($question->id)?->answer ? 'answered' : '' }}" data-jump="{{ $loop->index }}">{{ $loop->iteration }}</button>@endforeach</div></div></div></div>
    <div class="exam-modal-layer" id="finish-modal"><div class="exam-modal" style="max-width:480px;"><div class="exam-modal-body" style="text-align:center;padding:30px;"><i class="fas fa-triangle-exclamation" style="font-size:48px;color:#f59e0b;"></i><h2 style="margin:15px 0 8px;">Konfirmasi Mengakhiri Ujian</h2><p>Setelah dikirim, jawaban tidak dapat diubah kembali.</p><label style="display:flex;text-align:left;gap:10px;padding:14px;border:2px solid var(--border);border-radius:10px;margin:18px 0;"><input type="checkbox" id="finish-check" style="width:20px;min-height:20px;"> Saya telah memeriksa kembali semua jawaban.</label><div class="confirm-actions"><button class="btn btn-secondary" type="button" data-close>Batal</button><button class="btn btn-primary" type="button" id="confirm-finish" disabled>Ya, Selesai</button></div></div></div></div>
    <div class="exam-modal-layer" id="lock-modal"><div class="exam-modal" style="max-width:480px;"><div class="exam-modal-body" style="text-align:center;padding:32px;"><i class="fas fa-lock" style="font-size:48px;color:var(--danger);"></i><h2 style="margin:15px 0 8px;">Sesi Ujian Dikunci</h2><p style="margin-bottom:20px;">Sistem mendeteksi Anda meninggalkan halaman ujian. Hubungi guru untuk membuka kunci.</p><a href="{{ route('student.exams.index') }}" class="btn btn-primary">Kembali ke Daftar Ujian</a></div></div></div>

    <script>
        const csrf=document.querySelector('meta[name="csrf-token"]').content,questions=[...document.querySelectorAll('.exam-question')],jumps=[...document.querySelectorAll('.question-jump')];let current=0,reporting=false,examActive=true;const doubts=new Set();
        const show=i=>{current=Math.max(0,Math.min(i,questions.length-1));questions.forEach((q,n)=>q.classList.toggle('active',n===current));document.getElementById('previous').style.visibility=current?'visible':'hidden';document.getElementById('next').innerHTML=current===questions.length-1?'Selesai Ujian <i class="fas fa-circle-check"></i>':'Soal Berikutnya <i class="fas fa-chevron-right"></i>';document.getElementById('doubt').innerHTML=(doubts.has(current)?'<i class="fas fa-square-check"></i>':'<i class="far fa-square"></i>')+' Ragu-Ragu'};show(0);
        document.getElementById('previous').onclick=()=>show(current-1);document.getElementById('next').onclick=()=>current===questions.length-1?document.getElementById('finish-modal').classList.add('open'):show(current+1);document.getElementById('doubt').onclick=()=>{doubts.has(current)?doubts.delete(current):doubts.add(current);jumps[current].classList.toggle('doubted');show(current)};
        document.getElementById('open-navigation').onclick=()=>document.getElementById('navigation-modal').classList.add('open');document.querySelectorAll('[data-close]').forEach(b=>b.onclick=()=>document.querySelectorAll('.exam-modal-layer').forEach(m=>m.classList.remove('open')));jumps.forEach(b=>b.onclick=()=>{show(+b.dataset.jump);document.getElementById('navigation-modal').classList.remove('open')});
        const saveStatus=document.getElementById('save-status');
        setSaveStatus(navigator.onLine?'✓ Terhubung':'⚠ Koneksi internet terputus',navigator.onLine);
        const pendingKey='my_asssesmen:pending_answers:'+@json($participant->id);
        const readPending=()=>{try{return JSON.parse(localStorage.getItem(pendingKey)||'{}')}catch{return {}}};
        const writePending=data=>localStorage.setItem(pendingKey,JSON.stringify(data));
        const setSaveStatus=(text,ok=false)=>{saveStatus.textContent=text;saveStatus.style.color=ok?'#047857':'#b45309'};
        const pending=readPending();
        const postAnswer=async(questionId,answer)=>{
            const res=await fetch(@json(route('student.exams.answers.save',$participant)),{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:JSON.stringify({question_id:questionId,answer})});
            if(!res.ok) throw new Error('Gagal menyimpan jawaban');
        };
        const saveAnswer=async(input)=>{
            const questionId=+input.dataset.questionId,answer=input.value;
            const i=questions.indexOf(input.closest('.exam-question'));jumps[i].classList.add('answered');
            setSaveStatus('Menyimpan...');
            try{
                await postAnswer(questionId,answer);
                const p=readPending();delete p[questionId];writePending(p);
                setSaveStatus('✓ Jawaban tersimpan',true);
            }catch(e){
                const p=readPending();p[questionId]=answer;writePending(p);
                setSaveStatus('⚠ Belum tersimpan ke server');
            }
        };
        document.querySelectorAll('input[data-question-id]').forEach(input=>input.onchange=()=>saveAnswer(input));
        const flushPending=async()=>{
            const p=readPending(),entries=Object.entries(p);
            if(!entries.length)return true;
            setSaveStatus('Menyinkronkan jawaban...');
            for(const [questionId,answer] of entries){
                try{await postAnswer(+questionId,answer);const latest=readPending();delete latest[questionId];writePending(latest)}catch(e){setSaveStatus('⚠ Ada jawaban yang belum tersimpan');return false}
            }
            setSaveStatus('✓ Semua jawaban tersimpan',true);return true;
        };
        window.addEventListener('online',flushPending);
        const check=document.getElementById('finish-check'),confirm=document.getElementById('confirm-finish');check.onchange=()=>confirm.disabled=!check.checked;
        confirm.onclick=async()=>{confirm.disabled=true;const ok=await flushPending();if(!ok){alert('Koneksi internet bermasalah. Pastikan semua jawaban sudah tersimpan sebelum mengakhiri ujian.');confirm.disabled=false;return}examActive=false;document.getElementById('submit-form').submit()};
        const end=new Date(@json($endTimeIso)),timer=document.getElementById('timer');setInterval(async()=>{const d=end-new Date();if(d<=0){const ok=await flushPending();if(ok){examActive=false;document.getElementById('submit-form').submit()}return}const s=Math.floor(d/1000);timer.textContent=String(Math.floor(s/60)).padStart(2,'0')+':'+String(s%60).padStart(2,'0')},1000);
        window.addEventListener('offline',()=>setSaveStatus('⚠ Koneksi internet terputus'));
        document.addEventListener('visibilitychange',async()=>{if(document.hidden&&examActive&&!reporting){reporting=true;try{await fetch(@json(route('student.exams.violation',$participant)),{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:JSON.stringify({reason:'Meninggalkan halaman ujian / membuka tab lain'})});document.getElementById('lock-modal').classList.add('open')}finally{reporting=false}}});
    </script>
</x-layouts.student>
