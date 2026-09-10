<x-layouts.app :title="'Akun Guru - My Asssesmen'">
    <section class="card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: var(--text);">Data Guru</h2>
                <span style="background: rgba(59, 130, 246, 0.1); color: var(--primary); padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">{{ count($teachers) }} Guru</span>
            </div>
            
            <div style="display: flex; gap: 16px; align-items: center;">
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama guru..." style="padding: 10px 16px 10px 40px; border-radius: 8px; border: 1px solid var(--border); width: 260px; font-size: 14px; background: #fff; transition: all 0.2s;">
                </div>
                <button type="button" class="btn btn-primary" onclick="openTambahGuruModal()" style="display: flex; align-items: center; gap: 8px; padding: 10px 20px;">
                    <i class="fas fa-plus"></i> Tambah Guru
                </button>
            </div>
        </div>

        <div class="table-wrap">
            <table class="hero-table" id="teacherTable">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid var(--border);">
                        <th style="padding: 16px 20px; text-align: center; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Nama Lengkap</th>
                        <th style="padding: 16px 20px; text-align: center; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Username</th>
                        <th style="padding: 16px 20px; text-align: center; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">NIP / ID</th>
                        <th style="padding: 16px 20px; text-align: center; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Gender</th>
                        <th style="padding: 16px 20px; text-align: center; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    @forelse($teachers as $teacher)
                        <tr style="border-bottom: 1px solid var(--border); transition: all 0.2s; background: #fff;" class="teacher-row">
                            <td style="padding: 16px 20px; text-align: center;">
                                <div style="display: flex; align-items: center; gap: 12px; justify-content: center;">
                                    <div style="font-weight: 600; color: #1e293b;" class="teacher-name">{{ $teacher->user->name }}</div>
                                </div>
                            </td>
                            <td style="padding: 16px 20px; text-align: center;">
                                <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; color: #475569; font-family: monospace; font-size: 13px;">{{ $teacher->user->username }}</span>
                            </td>
                            <td style="padding: 16px 20px; color: #475569; text-align: center;">{{ $teacher->nip ?? '-' }}</td>
                            <td style="padding: 16px 20px; color: #475569; text-align: center;">
                                @if($teacher->user->gender === 'L')
                                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 500;"><i class="fas fa-mars"></i> Laki-laki</span>
                                @elseif($teacher->user->gender === 'P')
                                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #fdf4ff; color: #d946ef; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 500;"><i class="fas fa-venus"></i> Perempuan</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 16px 20px;">
                                <div style="display: flex; justify-content: center; gap: 8px;">
                                    <button type="button" class="btn btn-secondary open-update-modal"
                                        style="padding: 6px 12px; font-size: 13px; display: flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #cbd5e1; color: #475569;"
                                        data-action="{{ route('admin.teachers.update', $teacher) }}"
                                        data-name="{{ $teacher->user->name }}"
                                        data-username="{{ $teacher->user->username }}"
                                        data-nip="{{ $teacher->nip }}"
                                        data-gender="{{ $teacher->user->gender }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger"
                                        style="padding: 6px 12px; font-size: 13px; display: flex; align-items: center; gap: 6px;"
                                        onclick="confirmGlobalDelete('{{ route('admin.teachers.destroy', $teacher) }}', 'Apakah Anda yakin ingin menghapus guru {{ addslashes($teacher->user->name) }} secara permanen?')">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px 20px; text-align: center; color: #64748b;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                                    <i class="fas fa-user-slash" style="font-size: 32px; color: #cbd5e1;"></i>
                                    <span>Belum ada data guru. Klik "Tambah Guru" untuk menambahkan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal Tambah/Edit Guru -->
    <div id="teacherModal" class="modal" style="display: none; z-index: 100;">
        <div class="modal-content" style="max-width: 500px; padding: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
            <div style="background: #f8fafc; padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="teacherModalTitle" style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-plus" style="color: var(--primary);"></i> Tambah Guru Baru
                </h3>
            </div>
            
            <form id="teacherForm" method="POST" action="{{ route('admin.teachers.store') }}" style="padding: 24px;">
                @csrf
                <input type="hidden" name="_method" id="teacherMethod" value="POST">
                
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Nama Lengkap Guru <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="name" id="input_name" required placeholder="Contoh: Budi Santoso, S.Pd" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; transition: all 0.2s; box-sizing: border-box;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Username <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="username" id="input_username" required placeholder="budi.s" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; transition: all 0.2s; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
                            <div style="position: relative;">
                                <select name="gender" id="input_gender" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; appearance: none; background-color: #fff; cursor: pointer; box-sizing: border-box;">
                                    <option value="" disabled selected>Pilih...</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <i class="fas fa-chevron-down" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 12px;"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">NIP / ID</label>
                        <input type="text" name="nip" id="input_nip" inputmode="numeric" pattern="\d{18}" maxlength="18" placeholder="Opsional, 18 digit angka" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 14px; transition: all 0.2s; box-sizing: border-box;">
                        <span style="font-size: 12px; color: #94a3b8; margin-top: 4px; display: block;">Kosongkan jika bukan ASN.</span>
                    </div>

                    <div style="margin-top: 8px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 12px;"><i class="fas fa-shield-alt" style="color: #3b82f6; margin-right: 6px;"></i> Keamanan Akun</label>
                        
                        <input type="hidden" name="password" id="input_password" required>
                        
                        <button type="button" id="btnGeneratePassword" style="width: 100%; padding: 12px; background: #eff6ff; color: #2563eb; border: 1px dashed #93c5fd; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;">
                            <i class="fas fa-magic"></i> <span id="textGeneratePassword">Generate Password Awal</span>
                        </button>
                        
                        <div id="passwordResultArea" style="display: none; margin-top: 12px;">
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-family: monospace; font-size: 16px; font-weight: 700; color: #166534; letter-spacing: 1px;" id="displayPassword"></div>
                                <button type="button" id="btnCopyPassword" title="Salin ke Clipboard" style="background: transparent; border: none; color: #22c55e; cursor: pointer; font-size: 16px; padding: 4px; transition: color 0.2s;">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            <span style="font-size: 12px; color: #64748b; margin-top: 8px; display: block; font-style: italic;">
                                *Catat password di atas. Password akan resmi diterapkan setelah Anda mengklik tombol Simpan di bawah.
                            </span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" class="btn btn-secondary" onclick="closeTeacherModal()" style="padding: 10px 20px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Handling
        const teacherModal = document.getElementById('teacherModal');
        const backdrop = document.getElementById('modalBackdrop');
        const teacherForm = document.getElementById('teacherForm');
        
        function openTeacherModal() {
            teacherModal.style.display = 'flex';
            if(backdrop) backdrop.style.display = 'block';
        }
        
        function closeTeacherModal() {
            teacherModal.style.display = 'none';
            if(backdrop) backdrop.style.display = 'none';
        }

        function resetPasswordUI() {
            document.getElementById('input_password').value = '';
            document.getElementById('passwordResultArea').style.display = 'none';
        }

        function openTambahGuruModal() {
            document.getElementById('teacherModalTitle').innerHTML = '<i class="fas fa-user-plus" style="color: var(--primary);"></i> Tambah Guru Baru';
            teacherForm.action = "{{ route('admin.teachers.store') }}";
            document.getElementById('teacherMethod').value = 'POST';
            
            // Clear inputs
            document.getElementById('input_name').value = '';
            document.getElementById('input_username').value = '';
            document.getElementById('input_nip').value = '';
            document.getElementById('input_gender').value = '';
            
            // Password logic setup
            document.getElementById('input_password').required = true;
            document.getElementById('textGeneratePassword').innerText = 'Generate Password Awal';
            resetPasswordUI();
            
            openTeacherModal();
        }

        document.querySelectorAll('.open-update-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const name = this.getAttribute('data-name') || '';
                const username = this.getAttribute('data-username') || '';
                const nip = this.getAttribute('data-nip') || '';
                const gender = this.getAttribute('data-gender') || '';

                document.getElementById('teacherModalTitle').innerHTML = '<i class="fas fa-user-edit" style="color: var(--primary);"></i> Perbarui Guru';
                teacherForm.action = action;
                document.getElementById('teacherMethod').value = 'PUT';
                
                // Fill inputs
                document.getElementById('input_name').value = name;
                document.getElementById('input_username').value = username;
                document.getElementById('input_nip').value = nip;
                document.getElementById('input_gender').value = gender;
                
                // Password logic setup
                document.getElementById('input_password').required = false; // Not required for update
                document.getElementById('textGeneratePassword').innerText = 'Reset Password';
                resetPasswordUI();
                
                openTeacherModal();
            });
        });

        // Close when clicking outside
        if(backdrop) {
            backdrop.addEventListener('click', function(e) {
                if (e.target === backdrop) {
                    closeTeacherModal();
                }
            });
        }

        // Search Functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.teacher-row');
            
            rows.forEach(row => {
                const name = row.querySelector('.teacher-name').textContent.toLowerCase();
                if (name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Generate Password Logic
        document.getElementById('btnGeneratePassword').addEventListener('click', function() {
            const nip = document.getElementById('input_nip').value.trim();
            const username = document.getElementById('input_username').value.trim();
            
            let generatedPassword = '';
            
            if (nip && nip.length === 18) {
                generatedPassword = nip;
            } else if (username) {
                generatedPassword = username;
            } else {
                showToast('Isi Username atau NIP terlebih dahulu untuk generate password.', 'error');
                return;
            }
            
            document.getElementById('input_password').value = generatedPassword;
            document.getElementById('displayPassword').innerText = generatedPassword;
            document.getElementById('passwordResultArea').style.display = 'block';
            
            // Add subtle animation
            const resultArea = document.getElementById('passwordResultArea');
            resultArea.animate([
                { opacity: 0, transform: 'translateY(-10px)' },
                { opacity: 1, transform: 'translateY(0)' }
            ], { duration: 300, easing: 'ease-out' });
        });

        // Copy Password Logic
        document.getElementById('btnCopyPassword').addEventListener('click', function() {
            const password = document.getElementById('displayPassword').innerText;
            if (password) {
                navigator.clipboard.writeText(password).then(() => {
                    const icon = this.querySelector('i');
                    icon.className = 'fas fa-check';
                    this.style.color = '#16a34a';
                    showToast('Password berhasil disalin!', 'success');
                    
                    setTimeout(() => {
                        icon.className = 'far fa-copy';
                        this.style.color = '#22c55e';
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    showToast('Gagal menyalin password.', 'error');
                });
            }
        });
    </script>
</x-layouts.app>
