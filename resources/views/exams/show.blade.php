<x-layouts.app :title="$exam->title.' - My Asssesmen'">
    <a href="{{ route('exams.index') }}" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; color:#64748b; font-weight:600; font-size:15px; margin-bottom:16px; transition:color 0.2s;">
        <span style="display:flex; align-items:center; justify-content:center; width:24px; height:24px; background:var(--primary); color:white; border-radius:50%; font-size:12px;">
            <i class="fas fa-arrow-left"></i>
        </span>
        Kembali ke Daftar Ujian
    </a>

    {{-- Compact Header Card --}}
    <section class="card" style="padding:20px 24px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap;">
                    <h1 style="margin:0; font-size:22px;">{{ $exam->title }}</h1>
                    <span class="status-badge {{ $exam->getComputedStatus() }}">{{ ucfirst($exam->getComputedStatus()) }}</span>
                </div>
                <div class="info-grid" style="margin-top:12px;">
                    <div class="info-item">
                        <span class="info-label">Mata Pelajaran</span>
                        <span class="info-value">{{ $exam->subject->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Guru Pengampu</span>
                        <span class="info-value">{{ $exam->teacher->user->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal</span>
                        <span class="info-value">{{ optional($exam->exam_date)->format('d M Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Durasi</span>
                        <span class="info-value">{{ $exam->duration }} menit</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tahun Ajaran</span>
                        <span class="info-value">{{ $exam->academic_year ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tipe</span>
                        <span class="info-value">{{ $exam->exam_type ?? '-' }}</span>
                    </div>
                </div>
                <div style="margin-top:12px;">
                    <span class="info-label" style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Kelas</span>
                    <div style="display:flex; flex-wrap:wrap; gap:4px; margin-top:4px;">
                        @forelse($exam->classes as $cls)
                            <span class="chip" style="padding:4px 10px; font-size:12px;">{{ $cls->display_name }}</span>
                        @empty
                            <span style="font-size:12px; color:#ef4444; font-weight:600;">Belum ada kelas</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap; flex-shrink:0;">
                <a class="btn btn-secondary" href="{{ route('exams.edit', $exam) }}" style="font-size:13px; padding:0 14px; min-height:38px;">
                    <i class="fas fa-pen-to-square"></i> Edit Ujian
                </a>
                <button type="button" class="btn btn-secondary" style="font-size:13px; padding:0 14px; min-height:38px;" onclick="openParticipantsModal()">
                    <i class="fas fa-users"></i> Peserta ({{ $exam->participants->count() }})
                </button>
                <button type="button" class="btn" style="background:#fee2e2; color:#b91c1c; font-size:13px; padding:0 14px; min-height:38px;" onclick="confirmGlobalDelete('{{ route('exams.destroy', $exam) }}', 'Apakah Anda yakin ingin menghapus ujian {{ addslashes($exam->title) }}?')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </section>

    {{-- Collapsible Import Excel Section --}}
    <div class="import-section">
        <div class="import-section-header" onclick="toggleImport()">
            <div class="import-title">
                <i class="fas fa-file-excel"></i>
                <span>Impor Bank Soal dari Excel</span>
                <span style="font-size:12px; color:var(--text-muted); font-weight:500;">— Upload file template untuk impor otomatis</span>
            </div>
            <i id="importChevron" class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="importBody" class="import-section-body">
            <form method="POST" action="{{ route('exams.questions.import', $exam) }}" enctype="multipart/form-data" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                @csrf
                <input type="file" name="questions_file" accept=".xlsx,.xls,.csv" required style="flex:1; min-width:200px; max-width:400px;">
                <button class="btn btn-primary" type="submit" style="font-size:13px; min-height:38px;">
                    <i class="fas fa-file-import"></i> Impor Soal
                </button>
                <a class="btn btn-secondary" href="{{ route('exams.questions.template') }}" style="font-size:13px; min-height:38px;">
                    <i class="fas fa-download"></i> Template
                </a>
            </form>
        </div>
    </div>

    {{-- Bank Soal — Google Form Style --}}
    <section>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="margin:0; font-size:20px; font-weight:800;">
                <i class="fas fa-list-check" style="color:var(--primary); margin-right:8px;"></i>
                Bank Soal ({{ $exam->questions->count() }})
            </h2>
            <a class="btn btn-primary" href="{{ route('exams.questions.create', $exam) }}" style="font-size:13px; min-height:38px;">
                <i class="fas fa-plus"></i> Tambah Soal
            </a>
        </div>

        @forelse ($exam->questions as $question)
            <div class="question-card">
                <div class="question-card-header">
                    <div class="q-number">
                        <span>{{ $loop->iteration }}</span>
                        Soal {{ $loop->iteration }}
                    </div>
                    <div class="q-actions">
                        <a href="{{ route('exams.questions.edit', [$exam, $question]) }}" title="Edit soal">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button type="button" title="Hapus soal" onclick="confirmGlobalDelete('{{ route('exams.questions.destroy', [$exam, $question]) }}', 'Hapus soal nomor {{ $loop->iteration }}?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="question-card-body">
                    @if($question->image_path)
                        <img src="{{ asset('storage/'.$question->image_path) }}" alt="Gambar soal" class="q-image">
                    @endif
                    <div class="q-text">{{ $question->question }}</div>
                    <div class="question-options">
                        @foreach (['A','B','C','D','E'] as $opt)
                            @php $optKey = 'option_'.strtolower($opt); @endphp
                            @if($question->$optKey)
                                <div class="question-option {{ $question->correct_answer === $opt ? 'correct' : '' }}">
                                    <div class="opt-letter">{{ $opt }}</div>
                                    <span>{{ $question->$optKey }}</span>
                                    @if($question->correct_answer === $opt)
                                        <i class="fas fa-check" style="margin-left:auto; font-size:12px;"></i>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="question-card-footer">
                    <span>Bobot: {{ $question->score_weight }}</span>
                    <span>Kunci: {{ $question->correct_answer }}</span>
                </div>
            </div>
        @empty
            <div class="card" style="text-align:center; padding:48px 24px;">
                <i class="fas fa-file-circle-plus" style="font-size:48px; color:var(--border); margin-bottom:16px;"></i>
                <p style="color:#64748b; font-size:15px; font-weight:600; margin-bottom:16px;">Belum ada soal. Mulai buat soal pertama untuk ujian ini.</p>
                <a class="btn btn-primary" href="{{ route('exams.questions.create', $exam) }}">
                    <i class="fas fa-plus"></i> Tambah Soal Pertama
                </a>
            </div>
        @endforelse
    </section>

    {{-- Participants Modal --}}
    <div id="participantsModal" class="modal participants-modal" style="display:none; z-index:10000;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-users" style="color:var(--primary); margin-right:8px;"></i> Daftar Peserta (<span id="participantCount">{{ $exam->participants->count() }}</span>)</h3>
                <button class="close-btn" onclick="closeParticipantsModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="table-wrap">
                    <table id="participantsTable">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Nama</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($exam->participants as $participant)
                                <tr id="participant-row-{{ $participant->id }}">
                                    <td style="text-align:left; font-weight:600;">{{ $participant->student->user->name }}</td>
                                    <td>{{ $participant->student->nisn }}</td>
                                    <td>{{ $participant->student->schoolClass->display_name ?? '-' }}</td>
                                    <td>
                                        @if(!$participant->started_at)
                                            <button type="button" class="btn" style="background:#fee2e2; color:#b91c1c; padding:4px 10px; min-height:30px; font-size:12px;" onclick="removeParticipant({{ $participant->id }})">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @else
                                            <span style="font-size:12px; color:var(--text-muted);">Sudah mengerjakan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="padding:24px; color:var(--text-muted);">Belum ada peserta.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px; padding-top:16px; border-top:1px solid var(--border);">
                <a class="btn btn-primary" href="{{ route('exams.participants.create', $exam) }}" style="font-size:13px; min-height:36px;">
                    <i class="fas fa-user-plus"></i> Tambah Peserta
                </a>
                <button class="btn btn-secondary" onclick="closeParticipantsModal()" style="font-size:13px; min-height:36px;">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        // Import collapsible
        function toggleImport() {
            const body = document.getElementById('importBody');
            const chevron = document.getElementById('importChevron');
            body.classList.toggle('open');
            chevron.classList.toggle('open');
        }

        // Participants Modal
        function openParticipantsModal() {
            document.getElementById('participantsModal').style.display = 'flex';
            document.getElementById('modalBackdrop').style.display = 'block';
        }
        function closeParticipantsModal() {
            document.getElementById('participantsModal').style.display = 'none';
            document.getElementById('modalBackdrop').style.display = 'none';
        }

        // Remove participant via AJAX
        function removeParticipant(participantId) {
            if (!confirm('Hapus peserta ini dari ujian?')) return;
            
            fetch(`{{ url('/exams/'.$exam->id.'/participants') }}/${participantId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById(`participant-row-${participantId}`);
                    if (row) row.remove();
                    // Update count
                    const countEl = document.getElementById('participantCount');
                    countEl.textContent = parseInt(countEl.textContent) - 1;
                } else {
                    alert(data.error || 'Gagal menghapus peserta.');
                }
            })
            .catch(() => alert('Terjadi kesalahan.'));
        }
    </script>
</x-layouts.app>
