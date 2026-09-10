<x-layouts.app :title="'Akun Siswa - My Asssesmen'">

    {{-- Success Modal for student created --}}
    @if(session('status_student_created'))
    <div id="studentCreatedModal" style="position:fixed;inset:0;z-index:10001;display:flex;align-items:center;justify-content:center;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);"></div>
        <div style="position:relative;background:#fff;border-radius:20px;padding:40px 36px;max-width:400px;width:90%;text-align:center;box-shadow:0 25px 50px -12px rgba(0,0,0,0.2);animation:scaleIn 0.35s cubic-bezier(0.34,1.56,0.64,1);">
            <img src="{{ asset('images/auth/success-student.png') }}" alt="Berhasil" style="width:100px;margin:0 auto 16px;display:block;">
            <h3 style="font-size:22px;font-weight:700;color:#0f172a;margin:0 0 10px;">Akun Siswa Berhasil Dibuat!</h3>
            <p style="color:#64748b;font-size:14px;line-height:1.6;margin:0 0 24px;">Sistem telah otomatis membuat <strong>Username (NISN)</strong> dan <strong>Kata Sandi</strong> dari NISN siswa ini.</p>
            <button onclick="document.getElementById('studentCreatedModal').style.display='none'" style="width:100%;padding:14px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;transition:all 0.2s;">Selesai</button>
        </div>
    </div>
    @endif

    {{-- Import Result Modal --}}
    @if(session('status_import'))
    @php
        $importResult = json_decode(session('status_import'), true);
        $importCreated = $importResult['created'] ?? 0;
        $importFailed  = $importResult['failed']  ?? 0;
        $importFailures = $importResult['failures'] ?? [];
    @endphp
    <div id="importSuccessModal" style="position:fixed;inset:0;z-index:10001;display:flex;align-items:center;justify-content:center;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);"></div>
        <div style="position:relative;background:#fff;border-radius:20px;padding:36px 32px;max-width:480px;width:92%;text-align:center;box-shadow:0 25px 50px -12px rgba(0,0,0,0.2);animation:scaleIn 0.35s cubic-bezier(0.34,1.56,0.64,1);max-height:85vh;overflow-y:auto;">
            @if($importFailed === 0)
                <img src="{{ asset('images/auth/success-student.png') }}" alt="Berhasil" style="width:100px;margin:0 auto 16px;display:block;">
                <h3 style="font-size:20px;font-weight:700;color:#0f172a;margin:0 0 8px;">Import Berhasil!</h3>
                <p style="color:#64748b;font-size:14px;line-height:1.6;margin:0 0 20px;">
                    <strong style="color:#16a34a;font-size:18px;">{{ $importCreated }}</strong> siswa berhasil diimpor.<br>
                    Username dan password = <strong>NISN</strong> masing-masing siswa.
                </p>
            @elseif($importCreated > 0)
                <div style="font-size:48px;margin-bottom:12px;">⚠️</div>
                <h3 style="font-size:20px;font-weight:700;color:#0f172a;margin:0 0 8px;">Import Selesai dengan Peringatan</h3>
                <div style="display:flex;gap:16px;justify-content:center;margin:16px 0;">
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 20px;">
                        <div style="font-size:24px;font-weight:700;color:#16a34a;">{{ $importCreated }}</div>
                        <div style="font-size:12px;color:#15803d;font-weight:500;">Berhasil</div>
                    </div>
                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 20px;">
                        <div style="font-size:24px;font-weight:700;color:#dc2626;">{{ $importFailed }}</div>
                        <div style="font-size:12px;color:#b91c1c;font-weight:500;">Gagal / Dilewati</div>
                    </div>
                </div>
            @else
                <div style="font-size:48px;margin-bottom:12px;">❌</div>
                <h3 style="font-size:20px;font-weight:700;color:#0f172a;margin:0 0 8px;">Import Gagal</h3>
                <p style="color:#64748b;font-size:14px;margin:0 0 12px;">Tidak ada siswa yang berhasil diimpor. Periksa data Anda.</p>
            @endif

            @if(count($importFailures) > 0)
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px;text-align:left;margin-bottom:20px;max-height:200px;overflow-y:auto;">
                    <div style="font-size:13px;font-weight:700;color:#dc2626;margin-bottom:8px;display:flex;align-items:center;gap:6px;"><i class="fas fa-exclamation-triangle"></i> Data yang tidak berhasil diimpor:</div>
                    @foreach($importFailures as $fail)
                        <div style="font-size:12px;color:#7f1d1d;padding:4px 0;border-bottom:1px solid #fee2e2;line-height:1.5;">• {{ $fail }}</div>
                    @endforeach
                </div>
            @endif

            <button onclick="document.getElementById('importSuccessModal').style.display='none'" style="width:100%;padding:13px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;">Selesai</button>
        </div>
    </div>
    @endif


    <style>
        @keyframes scaleIn {
            from { transform: scale(0.8); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }
    </style>

    {{-- Top Controls --}}
    <section class="card" style="padding: 20px 24px; margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h2 style="margin: 0 0 4px; font-size: 20px; font-weight: 700; color: var(--text);">Data Siswa</h2>
                <p style="margin: 0; font-size: 13px; color: #64748b;">{{ count($students) }} siswa terdaftar di sistem</p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                {{-- Search --}}
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama / NISN..." style="padding: 10px 16px 10px 38px; border-radius: 8px; border: 1px solid var(--border); width: 240px; font-size: 14px; background: #fff;">
                </div>
                {{-- Per-page selector --}}
                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #475569;">
                    <span>Tampilkan:</span>
                    <select id="perPage" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border); font-size: 14px; cursor: pointer;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                {{-- Download Template --}}
                <a href="{{ route('admin.students.template') }}" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; font-size: 14px;">
                    <i class="fas fa-file-excel"></i> Template Excel
                </a>
                {{-- Import --}}
                <button type="button" onclick="document.getElementById('importSection').classList.toggle('hidden')" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; font-size: 14px;">
                    <i class="fas fa-file-import"></i> Import Excel
                </button>
                {{-- Add Student --}}
                <button type="button" onclick="openAddStudentModal()" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; font-size: 14px;">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
            </div>
        </div>

        {{-- Import section (hidden by default) --}}
        <div id="importSection" class="hidden" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
            @if($errors->has('students_file'))
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; color: #dc2626; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first('students_file') }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data" style="display: flex; gap: 12px; align-items: center;">
                @csrf
                <input type="file" name="students_file" accept=".xlsx,.xls" required style="flex: 1; padding: 8px; border: 1px dashed #94a3b8; border-radius: 8px; font-size: 14px;">
                <button class="btn btn-primary" type="submit" style="white-space: nowrap;"><i class="fas fa-upload"></i> Impor Sekarang</button>
            </form>
            <p style="margin: 8px 0 0; font-size: 12px; color: #94a3b8;">*Gunakan template Excel yang tersedia. Pastikan kolom: <strong>nama, nisn, kelas, jenis_kelamin</strong>. Username & password otomatis dari NISN.</p>
        </div>
    </section>

    {{-- Table --}}
    <section class="card table-wrap" style="margin-top: 16px; padding: 16px;">
        <div>
            <table id="studentTable" class="hero-table">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid var(--border);">
                        <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">#</th>
                        <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Nama Siswa</th>
                        <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">NISN</th>
                        <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Jenis Kelamin</th>
                        <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Kelas</th>
                        <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    @forelse($students as $idx => $student)
                    <tr class="student-row" data-name="{{ strtolower($student->user->name) }}" data-nisn="{{ $student->nisn }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;">
                        <td style="padding: 12px 16px; text-align: center; color: #94a3b8; font-size: 13px;" class="row-number">{{ $idx + 1 }}</td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">

                                <span style="font-weight: 600; color: #1e293b;">{{ $student->user->name }}</span>
                            </div>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;"><span style="background: #f1f5f9; padding: 3px 10px; border-radius: 6px; color: #475569; font-family: monospace; font-size: 13px;">{{ $student->nisn }}</span></td>
                        <td style="padding: 12px 16px; text-align: center;">
                            @if($student->user->gender === 'L')
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #eff6ff; color: #3b82f6; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;"><i class="fas fa-mars"></i> Laki-laki</span>
                            @elseif($student->user->gender === 'P')
                                <span style="display: inline-flex; align-items: center; gap: 5px; background: #fdf4ff; color: #d946ef; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;"><i class="fas fa-venus"></i> Perempuan</span>
                            @else
                                <span style="color: #94a3b8; font-size: 13px;">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <span style="background: #f0fdf4; color: #16a34a; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ $student->schoolClass->display_name ?? '-' }}</span>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 6px;">
                                <button type="button"
                                    class="btn open-edit-student"
                                    style="padding: 6px 12px; font-size: 12px; background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; display: flex; align-items: center; gap: 5px;"
                                    data-id="{{ $student->id }}"
                                    data-name="{{ $student->user->name }}"
                                    data-nisn="{{ $student->nisn }}"
                                    data-gender="{{ $student->user->gender }}"
                                    data-classid="{{ $student->class_id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button"
                                    class="btn btn-danger"
                                    style="padding: 6px 12px; font-size: 12px; display: flex; align-items: center; gap: 5px;"
                                    onclick="confirmGlobalDelete('{{ route('admin.students.destroy', $student) }}', 'Hapus akun siswa &quot;{{ addslashes($student->user->name) }}&quot; (NISN: {{ $student->nisn }}) secara permanen?')">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 48px 20px; text-align: center; color: #64748b;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                                <i class="fas fa-user-graduate" style="font-size: 36px; color: #cbd5e1;"></i>
                                <span>Belum ada data siswa. Klik "Tambah Siswa" atau import melalui Excel.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination controls --}}
        <div style="padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <span id="paginationInfo" style="font-size: 13px; color: #64748b;"></span>
            <div style="display: flex; gap: 6px;" id="paginationBtns"></div>
        </div>
    </section>

    {{-- ADD / EDIT Student Modal --}}
    <div id="studentModal" class="modal" style="display: none; z-index: 100;">
        <div class="modal-content" style="max-width: 520px; padding: 0; border-radius: 16px; overflow: hidden;">
            <div style="background: #f8fafc; padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
                <h3 id="studentModalTitle" style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-graduate" style="color: var(--primary);"></i> Tambah Siswa Baru
                </h3>
            </div>
            <form id="studentForm" method="POST" action="{{ route('admin.students.store') }}" style="padding: 24px;">
                @csrf
                <input type="hidden" name="_method" id="studentMethod" value="POST">
                <div style="display: flex; flex-direction: column; gap: 16px;">

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px;">Nama Lengkap <span style="color:#ef4444">*</span></label>
                        <input type="text" name="name" id="s_name" required placeholder="Contoh: Budi Santoso" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #cbd5e1;font-size:14px;box-sizing:border-box;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px;">NISN <span style="color:#ef4444">*</span></label>
                            <input type="text" name="nisn" id="s_nisn" value="{{ old('nisn') }}" required inputmode="numeric" maxlength="10" placeholder="10 digit angka" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid @error('nisn') #ef4444 @else #cbd5e1 @enderror;font-size:14px;box-sizing:border-box;">
                            @error('nisn')
                                <span style="font-size:12px;color:#ef4444;display:block;margin-top:4px;">{{ $message }}</span>
                            @else
                                <span style="font-size:12px;color:#94a3b8;display:block;margin-top:4px;">Username & password = NISN</span>
                            @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px;">Jenis Kelamin <span style="color:#ef4444">*</span></label>
                            <div style="position:relative;">
                                <select name="gender" id="s_gender" required style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #cbd5e1;font-size:14px;appearance:none;background:#fff;box-sizing:border-box;">
                                    <option value="" disabled selected>Pilih...</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none;font-size:12px;"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px;">Kelas <span style="color:#ef4444">*</span></label>
                        <div style="position:relative;">
                            <select name="class_id" id="s_class" required style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #cbd5e1;font-size:14px;appearance:none;background:#fff;box-sizing:border-box;">
                                <option value="" disabled selected>Pilih kelas...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->display_name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;pointer-events:none;font-size:12px;"></i>
                        </div>
                    </div>

                    {{-- Password info box --}}
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-shield-alt" style="color:#16a34a;font-size:16px;flex-shrink:0;"></i>
                        <span style="font-size:13px;color:#15803d;">Username dan password akan otomatis diisi dari <strong>NISN</strong> siswa.</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" onclick="closeStudentModal()" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .hidden { display: none !important; }
        #studentTable tbody tr:hover { background: #f8fafc; }
        .page-btn { padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; font-size: 13px; font-weight: 500; color: #475569; transition: all 0.15s; }
        .page-btn.active { background: #2563eb; color: #fff; border-color: #2563eb; }
        .page-btn:hover:not(.active) { background: #f1f5f9; }
    </style>

    <script>
    // ============ Pagination & Search ============
    const allRows    = Array.from(document.querySelectorAll('.student-row'));
    const perPageSel = document.getElementById('perPage');
    const searchInp  = document.getElementById('searchInput');
    const infoEl     = document.getElementById('paginationInfo');
    const btnsEl     = document.getElementById('paginationBtns');
    let currentPage  = 1;

    function filteredRows() {
        const q = searchInp.value.toLowerCase().trim();
        return allRows.filter(r => !q || r.dataset.name.includes(q) || r.dataset.nisn.includes(q));
    }

    function renderTable() {
        const rows = filteredRows();
        const perPage = parseInt(perPageSel.value);
        const totalPages = Math.max(1, Math.ceil(rows.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        // hide all, show current page
        allRows.forEach(r => r.style.display = 'none');
        const start = (currentPage - 1) * perPage;
        const slice = rows.slice(start, start + perPage);
        slice.forEach((r, i) => {
            r.style.display = '';
            r.querySelector('.row-number').textContent = start + i + 1;
        });

        // Info text
        const from = rows.length === 0 ? 0 : start + 1;
        const to   = Math.min(start + perPage, rows.length);
        infoEl.textContent = `Menampilkan ${from}–${to} dari ${rows.length} siswa`;

        // Pagination buttons
        btnsEl.innerHTML = '';
        if (totalPages <= 1) return;

        const addBtn = (label, page, disabled, isActive) => {
            const b = document.createElement('button');
            b.className = 'page-btn' + (isActive ? ' active' : '');
            b.textContent = label;
            b.disabled = disabled;
            b.onclick = () => { currentPage = page; renderTable(); };
            btnsEl.appendChild(b);
        };
        addBtn('«', 1, currentPage === 1, false);
        addBtn('‹', currentPage - 1, currentPage === 1, false);

        let startP = Math.max(1, currentPage - 2);
        let endP   = Math.min(totalPages, startP + 4);
        if (endP - startP < 4) startP = Math.max(1, endP - 4);

        for (let p = startP; p <= endP; p++) addBtn(p, p, false, p === currentPage);

        addBtn('›', currentPage + 1, currentPage === totalPages, false);
        addBtn('»', totalPages, currentPage === totalPages, false);
    }

    perPageSel.addEventListener('change', () => { currentPage = 1; renderTable(); });
    searchInp.addEventListener('input', () => { currentPage = 1; renderTable(); });
    renderTable();

    // ============ Modal ============
    const studentModal = document.getElementById('studentModal');
    const studentForm  = document.getElementById('studentForm');
    const backdrop     = document.getElementById('modalBackdrop');

    function openStudentModal() {
        studentModal.style.display = 'flex';
        if (backdrop) backdrop.style.display = 'block';
    }
    function closeStudentModal() {
        studentModal.style.display = 'none';
        if (backdrop) backdrop.style.display = 'none';
    }

    function openAddStudentModal() {
        document.getElementById('studentModalTitle').innerHTML = '<i class="fas fa-user-plus" style="color:var(--primary);"></i> Tambah Siswa Baru';
        studentForm.action = "{{ route('admin.students.store') }}";
        document.getElementById('studentMethod').value = 'POST';
        document.getElementById('s_name').value    = '';
        document.getElementById('s_nisn').value    = '';
        document.getElementById('s_gender').value  = '';
        document.getElementById('s_class').value   = '';
        openStudentModal();
    }

    document.querySelectorAll('.open-edit-student').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('studentModalTitle').innerHTML = '<i class="fas fa-user-edit" style="color:var(--primary);"></i> Edit Data Siswa';
            studentForm.action = `/admin/students/${this.dataset.id}`;
            document.getElementById('studentMethod').value = 'PUT';
            document.getElementById('s_name').value   = this.dataset.name;
            document.getElementById('s_nisn').value   = this.dataset.nisn;
            document.getElementById('s_gender').value = this.dataset.gender;
            document.getElementById('s_class').value  = this.dataset.classid;
            openStudentModal();
        });
    });

    if (backdrop) {
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) closeStudentModal();
        });
    }

    @if($errors->any())
        // Auto-open modal if there are validation errors
        openStudentModal();
    @endif
    </script>

</x-layouts.app>
