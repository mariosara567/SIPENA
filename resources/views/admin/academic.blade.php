<x-layouts.app :title="'Data Akademik - My Asssesmen'">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.5px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
            min-height: auto;
        }

        .btn-danger-light {
            background-color: #fee2e2;
            color: #ef4444;
            border: 1px solid #fca5a5;
            font-weight: 600;
        }

        .btn-danger-light:hover {
            background-color: #fecaca;
            color: #b91c1c;
        }

        .btn-outline-primary {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: rgba(0, 102, 212, 0.05);
        }

        .text-center { text-align: center; }
        .mt-4 { margin-top: 24px; }
        .mb-4 { margin-bottom: 24px; }
        
        .hidden-row { display: none; }
        .show-all-container {
            text-align: center;
            padding: 8px 16px;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
        }
    </style>



    <!-- Kelas Card -->
    <article class="card mb-4">
        <div class="card-header">
            <h2 class="card-title">Kelas</h2>
            <button class="btn btn-primary" onclick="openModal('classModal')">
                <i class="fas fa-plus"></i> Tambah Kelas
            </button>
        </div>

        <div class="table-wrap">
            <table class="hero-table" id="classesTable">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th>Tingkat</th>
                        <th>Nama Kelas</th>
                        <th class="text-center">Jumlah Siswa</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $index => $class)
                        <tr class="{{ $index >= 5 ? 'hidden-row' : '' }}">
                            <td class="text-center" style="color: #94a3b8;">{{ $index + 1 }}</td>
                            <td>{{ $class->level }}</td>
                            <td style="font-weight: 600;">{{ $class->name }}</td>
                            <td class="text-center">{{ $class->students_count }}</td>
                            <td class="text-center">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editClass({{ $class->id }}, '{{ $class->name }}', {{ $class->level }})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger-light" type="button" onclick="confirmGlobalDelete('{{ route('admin.classes.destroy', $class) }}', 'Apakah Anda yakin ingin menghapus kelas {{ $class->name }} secara permanen?')">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 30px; color: #64748b;">Belum ada kelas yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if(count($classes) > 5)
                <div class="show-all-container" id="classesToggleContainer">
                    <button class="btn btn-outline-primary" onclick="toggleRows('classesTable', 'classesToggleBtn')" id="classesToggleBtn">
                        Lihat Semua Kelas ({{ count($classes) }})
                    </button>
                </div>
            @endif
        </div>
    </article>

    <!-- Mata Pelajaran Card -->
    <article class="card">
        <div class="card-header">
            <h2 class="card-title">Mata Pelajaran</h2>
            <button class="btn btn-primary" onclick="openModal('subjectModal')">
                <i class="fas fa-plus"></i> Tambah Mapel
            </button>
        </div>

        <div class="table-wrap">
            <table class="hero-table" id="subjectsTable">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th>Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Kelompok</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $index => $subject)
                        <tr class="{{ $index >= 5 ? 'hidden-row' : '' }}">
                            <td class="text-center" style="color: #94a3b8;">{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: #64748b;">{{ $subject->code ?? '-' }}</td>
                            <td style="font-weight: 600;">{{ $subject->name }}</td>
                            <td>{{ $subject->group ?? '-' }}</td>
                            <td class="text-center">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editSubject({{ $subject->id }}, '{{ $subject->name }}', '{{ $subject->code }}', '{{ $subject->group }}')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger-light" type="button" onclick="confirmGlobalDelete('{{ route('admin.subjects.destroy', $subject) }}', 'Apakah Anda yakin ingin menghapus mata pelajaran {{ $subject->name }} secara permanen?')">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 30px; color: #64748b;">Belum ada mata pelajaran yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if(count($subjects) > 5)
                <div class="show-all-container" id="subjectsToggleContainer">
                    <button class="btn btn-outline-primary" onclick="toggleRows('subjectsTable', 'subjectsToggleBtn')" id="subjectsToggleBtn">
                        Lihat Semua Mapel ({{ count($subjects) }})
                    </button>
                </div>
            @endif
        </div>
    </article>

    <!-- Modals -->
    <!-- Modal Kelas -->
    <div id="classModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 id="classModalTitle">Tambah Kelas</h3>
            </div>
            <form id="classForm" method="POST" action="{{ route('admin.classes.store') }}">
                @csrf
                <input type="hidden" name="_method" id="classMethod" value="POST">
                <div class="form-group">
                    <label>Tingkatan</label>
                    <select name="level" id="classLevel" required>
                        <option value="10">Kelas 10 (X)</option>
                        <option value="11">Kelas 11 (XI)</option>
                        <option value="12">Kelas 12 (XII)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-top: 16px;">
                    <label>Nama Kelas</label>
                    <input name="name" id="className" placeholder="Contoh: X IPA 1" required>
                </div>
                <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn" style="background: transparent; color: var(--text);" onclick="closeModal('classModal')">Batal</button>
                    <button type="submit" class="btn btn-primary" id="classSubmitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Mapel -->
    <div id="subjectModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3 id="subjectModalTitle">Tambah Mata Pelajaran</h3>
            </div>
            <form id="subjectForm" method="POST" action="{{ route('admin.subjects.store') }}">
                @csrf
                <input type="hidden" name="_method" id="subjectMethod" value="POST">
                <div class="form-group">
                    <label>Kode Mapel</label>
                    <input name="code" id="subjectCode" placeholder="Maks. 4 huruf (contoh: MAT)" maxlength="4" style="text-transform: uppercase;">
                </div>
                <div class="form-group" style="margin-top: 16px;">
                    <label>Nama Mata Pelajaran</label>
                    <input name="name" id="subjectName" placeholder="Contoh: Matematika" required>
                </div>
                <div class="form-group" style="margin-top: 16px;">
                    <label>Kelompok</label>
                    <select name="group" id="subjectGroup">
                        <option value="">-- Pilih Kelompok --</option>
                        <option value="Wajib">Wajib</option>
                        <option value="Pilihan">Pilihan</option>
                    </select>
                </div>
                <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn" style="background: transparent; color: var(--text);" onclick="closeModal('subjectModal')">Batal</button>
                    <button type="submit" class="btn btn-primary" id="subjectSubmitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleRows(tableId, btnId) {
            const table = document.getElementById(tableId);
            const hiddenRows = table.querySelectorAll('.hidden-row');
            const btn = document.getElementById(btnId);
            
            if (hiddenRows.length > 0) {
                // Show all
                hiddenRows.forEach(row => {
                    row.classList.remove('hidden-row');
                    row.classList.add('shown-row'); // mark them so we can hide them again if needed
                });
                btn.textContent = 'Sembunyikan Sebagian';
            } else {
                // Hide again
                const shownRows = table.querySelectorAll('.shown-row');
                shownRows.forEach(row => {
                    row.classList.add('hidden-row');
                    row.classList.remove('shown-row');
                });
                // Find total rows minus 5
                const totalRows = table.querySelectorAll('tbody tr').length;
                btn.textContent = `Lihat Semua (${totalRows})`;
            }
        }

        function openModal(id) {
            document.getElementById('modalBackdrop').style.display = 'block';
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById('modalBackdrop').style.display = 'none';
            document.getElementById(id).style.display = 'none';
            
            // Reset forms
            if (id === 'classModal') {
                document.getElementById('classForm').reset();
                document.getElementById('classForm').action = "{{ route('admin.classes.store') }}";
                document.getElementById('classMethod').value = "POST";
                document.getElementById('classModalTitle').innerText = "Tambah Kelas";
            } else if (id === 'subjectModal') {
                document.getElementById('subjectForm').reset();
                document.getElementById('subjectForm').action = "{{ route('admin.subjects.store') }}";
                document.getElementById('subjectMethod').value = "POST";
                document.getElementById('subjectModalTitle').innerText = "Tambah Mata Pelajaran";
            }
        }

        function editClass(id, name, level) {
            document.getElementById('classModalTitle').innerText = "Edit Kelas";
            document.getElementById('classForm').action = "/admin/classes/" + id;
            document.getElementById('classMethod').value = "PUT";
            
            document.getElementById('className').value = name;
            document.getElementById('classLevel').value = level;
            
            openModal('classModal');
        }

        function editSubject(id, name, code, group) {
            document.getElementById('subjectModalTitle').innerText = "Edit Mata Pelajaran";
            document.getElementById('subjectForm').action = "/admin/subjects/" + id;
            document.getElementById('subjectMethod').value = "PUT";
            
            document.getElementById('subjectName').value = name;
            document.getElementById('subjectCode').value = code || '';
            document.getElementById('subjectGroup').value = group || '';
            
            openModal('subjectModal');
        }
    </script>
</x-layouts.app>
