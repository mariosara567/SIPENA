<x-layouts.app :title="'Manajemen Ujian - My Asssesmen'">
    {{-- Filter Bar: 1 row with + icon --}}
    <section class="card" style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; padding:16px 24px;">
        <div style="display:flex; gap:12px; align-items:center; flex:1; min-width:0; flex-wrap:wrap;">
            <select id="filter_subject" style="min-width:200px; max-width:280px; flex:1;" onchange="filterExams()">
                <option value="">Semua Mata Pelajaran</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                @endforeach
            </select>
            <select id="filter_class" style="min-width:200px; max-width:280px; flex:1;" onchange="filterExams()">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class->name }}">{{ $class->display_name }}</option>
                @endforeach
            </select>
        </div>
        <a class="btn btn-primary" href="{{ route('exams.create') }}" style="white-space:nowrap; flex-shrink:0;">
            <i class="fas fa-plus"></i> Buat Soal
        </a>
    </section>

    {{-- Exam Cards Grid --}}
    <section>
        <div class="exam-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:20px;">
            @forelse ($exams as $exam)
                <div class="card exam-card" data-subject="{{ $exam->subject->name }}" data-classes="{{ implode(',', $exam->classes->pluck('name')->toArray()) }}" style="padding:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                            <span style="font-size:11px; font-weight:700; color:var(--primary); background:rgba(0,102,212,0.08); padding:3px 10px; border-radius:99px;">
                                {{ $exam->academic_year ?? 'N/A' }}
                            </span>
                            <span style="font-size:11px; font-weight:700; color:#0f766e; background:#ccfbf1; padding:3px 10px; border-radius:99px;">
                                {{ $exam->exam_type ?? 'N/A' }}
                            </span>
                        </div>
                        <span class="status-badge {{ $exam->getComputedStatus() }}">{{ ucfirst($exam->getComputedStatus()) }}</span>
                    </div>
                    
                    <h3 style="margin:0 0 6px; font-size:17px; font-weight:800; letter-spacing:-0.3px;">{{ $exam->title }}</h3>
                    <p style="margin:0 0 4px; color:#475569; font-weight:600; font-size:13px;">
                        <i class="fas fa-book-open" style="margin-right:4px; color:var(--primary); font-size:12px;"></i>
                        {{ $exam->subject->name }}
                    </p>
                    <p style="margin:0; color:#64748b; font-size:12px; font-weight:500;">
                        <i class="fas fa-calendar" style="margin-right:4px;"></i>
                        {{ optional($exam->exam_date)->format('d M Y') ?? '-' }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-clock" style="margin-right:2px;"></i>
                        {{ $exam->duration }} menit
                        &nbsp;·&nbsp;
                        <i class="fas fa-list" style="margin-right:2px;"></i>
                        {{ $exam->questions_count }} soal
                    </p>
                    
                    <div style="margin:10px 0;">
                        <div style="display:flex; flex-wrap:wrap; gap:4px;">
                            @forelse($exam->classes as $cls)
                                <span style="font-size:11px; background:#f1f5f9; padding:2px 8px; border-radius:12px; color:#475569; font-weight:500;">{{ $cls->display_name }}</span>
                            @empty
                                <span style="font-size:11px; color:#ef4444;">Belum ada kelas</span>
                            @endforelse
                        </div>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; border-top:1px solid #e2e8f0; padding-top:12px;">
                        <div style="font-size:11px; color:#94a3b8; font-weight:500;">
                            {{ $exam->updated_at->diffForHumans() }}
                        </div>
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-secondary" style="padding:6px 14px; font-size:13px; gap:6px;">
                            <i class="fas fa-eye"></i> Lihat / Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="card" style="grid-column:1/-1; text-align:center; padding:60px 40px;">
                    <i class="fas fa-clipboard-list" style="font-size:48px; color:var(--border); margin-bottom:16px;"></i>
                    <p style="color:#64748b; font-size:16px; font-weight:600;">Belum ada data ujian yang dibuat.</p>
                    <a class="btn btn-primary" href="{{ route('exams.create') }}" style="margin-top:16px;">
                        <i class="fas fa-plus"></i> Buat Soal Pertama
                    </a>
                </div>
            @endforelse
        </div>
    </section>

    <script>
        function filterExams() {
            const subject = document.getElementById('filter_subject').value.toLowerCase();
            const cls = document.getElementById('filter_class').value.toLowerCase();
            const cards = document.querySelectorAll('.exam-card');

            cards.forEach(card => {
                const cardSubject = card.dataset.subject.toLowerCase();
                const cardClasses = card.dataset.classes.toLowerCase();
                const cardClassesArray = cardClasses.split(',');
                
                let matchSubject = !subject || cardSubject.includes(subject);
                let matchClass = !cls || cardClassesArray.includes(cls);

                if (matchSubject && matchClass) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</x-layouts.app>
