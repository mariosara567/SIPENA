<x-layouts.app :title="'Edit Ujian - My Asssesmen'">
    <a href="{{ route('exams.show', $exam) }}" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; color:#64748b; font-weight:600; font-size:15px; margin-bottom:16px; transition:color 0.2s;">
        <span style="display:flex; align-items:center; justify-content:center; width:24px; height:24px; background:var(--primary); color:white; border-radius:50%; font-size:12px;">
            <i class="fas fa-arrow-left"></i>
        </span>
        Kembali ke Detail Ujian
    </a>

    <section class="card" style="border-top:5px solid var(--primary);">
        <h1 style="margin:0 0 4px; font-size:22px;">Edit Ujian</h1>
        <p style="margin:0; color:#64748b; font-size:14px;">Ujian yang sudah memiliki peserta mulai mengerjakan tidak dapat diubah.</p>
    </section>

    <section class="card">
        @if ($errors->any())
            <div style="background:#fee2e2; color:#b91c1c; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px; font-weight:600;">
                <ul style="margin:0; padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('exams.update', $exam) }}" id="examForm">
            @csrf @method('PUT')
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
                <label>Judul Ujian / Nama Paket Soal
                    <input name="title" value="{{ old('title', $exam->title) }}" placeholder="cth: UTS Informatika Kelas X-1" required>
                </label>

                <label>Mata Pelajaran
                    <select name="subject_id" required>
                        <option value="">Pilih mata pelajaran</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id', $exam->subject_id) == $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label>Tahun Pelajaran
                    <input type="text" name="academic_year" value="{{ old('academic_year', $exam->academic_year) }}" required>
                </label>

                <label>Tipe Ujian
                    <select name="exam_type" required>
                        <option value="">Pilih tipe ujian</option>
                        <option value="Ulangan Harian" @selected(old('exam_type', $exam->exam_type) == 'Ulangan Harian')>Ulangan Harian</option>
                        <option value="UTS / Ganjil" @selected(old('exam_type', $exam->exam_type) == 'UTS / Ganjil')>UTS / Ganjil</option>
                        <option value="UAS / Genap" @selected(old('exam_type', $exam->exam_type) == 'UAS / Genap')>UAS / Genap</option>
                        <option value="Ujian Sekolah" @selected(old('exam_type', $exam->exam_type) == 'Ujian Sekolah')>Ujian Sekolah</option>
                    </select>
                </label>

                @if ($isAdministrator)
                    <label>Guru Pengampu
                        <select name="teacher_id" required>
                            <option value="">Pilih guru</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(old('teacher_id', $exam->teacher_id) == $teacher->id)>{{ $teacher->user->name }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                <label>Tanggal Ujian
                    <input type="date" name="exam_date" value="{{ old('exam_date', optional($exam->exam_date)->format('Y-m-d')) }}" required>
                </label>

                <label>Durasi Ujian
                    <select name="duration" required>
                        <option value="30" @selected(old('duration', $exam->duration) == '30')>30 Menit</option>
                        <option value="45" @selected(old('duration', $exam->duration) == '45')>45 Menit</option>
                        <option value="60" @selected(old('duration', $exam->duration) == '60')>60 Menit</option>
                        <option value="75" @selected(old('duration', $exam->duration) == '75')>75 Menit</option>
                        <option value="90" @selected(old('duration', $exam->duration) == '90')>90 Menit</option>
                        <option value="120" @selected(old('duration', $exam->duration) == '120')>120 Menit</option>
                    </select>
                </label>
            </div>

            {{-- Class Multi-Select with Chips --}}
            <div style="margin-top:20px;">
                <label style="margin-bottom:4px;">Kelas yang Mengerjakan</label>
                <div class="multi-select-wrapper">
                    <div id="classSelectTrigger" onclick="toggleClassDropdown()" style="min-height:44px; border:2px solid var(--border); border-radius:8px; padding:8px 14px; cursor:pointer; display:flex; align-items:center; gap:8px; flex-wrap:wrap; transition:border-color 0.2s;">
                        <div id="chipContainer" class="chip-container" style="padding:0; min-height:auto; flex:1; display:flex; flex-wrap:wrap; gap:6px;">
                            <span id="classPlaceholder" style="color:var(--text-muted); font-size:14px; font-weight:500;">Klik untuk memilih kelas...</span>
                        </div>
                        <i class="fas fa-chevron-down" style="color:var(--text-muted); font-size:12px; flex-shrink:0;"></i>
                    </div>
                    <div id="classDropdown" class="multi-select-dropdown">
                        @php
                            $groupedClasses = $classes->groupBy('level');
                        @endphp
                        @foreach ($groupedClasses as $level => $levelClasses)
                            <div class="multi-select-group-title">Tingkat {{ $level }}</div>
                            @foreach ($levelClasses as $cls)
                                <div class="multi-select-option" data-id="{{ $cls->id }}" data-level="{{ $cls->level }}" data-name="{{ $cls->display_name }}" onclick="toggleClassOption(this)">
                                    <input type="checkbox" tabindex="-1" style="pointer-events:none;">
                                    <span>{{ $cls->display_name }}</span>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <div id="classHiddenInputs"></div>
                <small style="color:#64748b; margin-top:6px; display:block; font-size:12px;">
                    <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                    Pilih satu atau lebih kelas. Hanya kelas dengan tingkat yang sama yang bisa dipilih bersamaan.
                </small>
            </div>

            <div style="display:flex; gap:10px; margin-top:24px; padding-top:20px; border-top:2px solid var(--border);">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a class="btn btn-secondary" href="{{ route('exams.show', $exam) }}">Batal</a>
            </div>
        </form>
    </section>

    <script>
        let selectedClasses = [];
        let selectedLevel = null;
        @php
            $selectedClassIds = is_array(old('class_ids')) ? old('class_ids') : $exam->classes->pluck('id')->toArray();
        @endphp
        const preselectedIds = @json($selectedClassIds);

        function toggleClassDropdown() {
            document.getElementById('classDropdown').classList.toggle('open');
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('.multi-select-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('classDropdown').classList.remove('open');
            }
        });

        function toggleClassOption(el) {
            const id = parseInt(el.dataset.id);
            const level = el.dataset.level;
            const name = el.dataset.name;
            const checkbox = el.querySelector('input[type="checkbox"]');
            const idx = selectedClasses.findIndex(c => c.id === id);
            
            if (idx > -1) {
                selectedClasses.splice(idx, 1);
                el.classList.remove('selected');
                checkbox.checked = false;
                if (selectedClasses.length === 0) { selectedLevel = null; enableAllOptions(); }
            } else {
                if (selectedLevel !== null && level !== selectedLevel) return;
                selectedClasses.push({ id, level, name });
                el.classList.add('selected');
                checkbox.checked = true;
                selectedLevel = level;
                disableOtherLevels(level);
            }
            renderChips();
            renderHiddenInputs();
        }

        function removeChip(id) {
            const el = document.querySelector(`.multi-select-option[data-id="${id}"]`);
            if (el) { el.classList.remove('selected'); el.querySelector('input[type="checkbox"]').checked = false; }
            selectedClasses = selectedClasses.filter(c => c.id !== id);
            if (selectedClasses.length === 0) { selectedLevel = null; enableAllOptions(); }
            renderChips();
            renderHiddenInputs();
        }

        function enableAllOptions() {
            document.querySelectorAll('.multi-select-option').forEach(opt => opt.classList.remove('disabled'));
        }

        function disableOtherLevels(activeLevel) {
            document.querySelectorAll('.multi-select-option').forEach(opt => {
                opt.classList.toggle('disabled', opt.dataset.level !== activeLevel);
            });
        }

        function renderChips() {
            const container = document.getElementById('chipContainer');
            const placeholder = document.getElementById('classPlaceholder');
            container.querySelectorAll('.chip').forEach(c => c.remove());
            if (selectedClasses.length === 0) {
                placeholder.style.display = 'inline';
            } else {
                placeholder.style.display = 'none';
                selectedClasses.forEach(cls => {
                    const chip = document.createElement('span');
                    chip.className = 'chip';
                    chip.innerHTML = `${cls.name} <button type="button" class="chip-remove" onclick="event.stopPropagation(); removeChip(${cls.id})"><i class="fas fa-times"></i></button>`;
                    container.appendChild(chip);
                });
            }
        }

        function renderHiddenInputs() {
            const container = document.getElementById('classHiddenInputs');
            container.innerHTML = '';
            selectedClasses.forEach(cls => {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'class_ids[]'; input.value = cls.id;
                container.appendChild(input);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (preselectedIds && preselectedIds.length > 0) {
                preselectedIds.forEach(id => {
                    const el = document.querySelector(`.multi-select-option[data-id="${id}"]`);
                    if (el) toggleClassOption(el);
                });
            }
        });
    </script>
</x-layouts.app>
