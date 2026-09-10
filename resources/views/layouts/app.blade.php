<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'My Asesmen' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- CSS / Tailwind -->
    <style>
        :root {
            --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            --primary: #0066D4;
            --primary-light: #3b8cf2;
            --primary-dark: #004c9e;
            --secondary: #6366f1;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #ea580c;
            --bg: #F8F9FA;
            --surface: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 13px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-weight: 500;
            min-height: 100%;
        }

        body {
            display: grid;
            grid-template-columns: 240px 1fr;
            grid-template-rows: 75px auto;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            grid-row: 1 / -1;
            grid-column: 1;
            background: linear-gradient(180deg, #0066D4 0%, #004c9e 100%);
            color: white;
            padding: 20px 0;
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0, 102, 212, 0.25);
            z-index: 100;
            position: sticky;
            top: 0;
            height: 100vh;
            align-self: start;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 20px;
            margin-bottom: 30px;
            text-decoration: none;
            color: white;
            font-weight: 800;
            font-size: 18px;
        }

        .sidebar-brand .mark {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }

        .sidebar-section {
            margin-bottom: 24px;
        }

        .sidebar-section-title {
            padding: 0 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: var(--primary-light);
        }

        .sidebar-nav i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        /* Topbar */
        .topbar {
            grid-column: 2;
            grid-row: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            z-index: 50;
            position: sticky;
            top: 0;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 8px;
            background: var(--bg);
            color: var(--primary);
            font-weight: 600;
            font-size: 13px;
        }

        .logout-btn {
            min-height: 38px;
            padding: 0 16px;
            border: 1px solid var(--border);
            border-radius: 7px;
            background: var(--surface);
            color: var(--text);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: var(--bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Main Content */
        main {
            grid-column: 2;
            grid-row: 2;
            padding: 30px 30px 0 30px;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 75px);
        }

        /* Global Footer */
        .global-footer {
            margin-top: auto;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            padding: 24px 0;
            border-top: 1px solid var(--border);
        }

        .global-footer strong {
            color: var(--text);
            font-weight: 700;
        }

        /* Alert Messages */
        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert.success {
            background: #ecfdf5;
            color: #166534;
            border-left-color: var(--success);
        }

        .alert.error {
            background: #fef2f2;
            color: #b91c1c;
            border-left-color: var(--danger);
        }

        .alert i {
            font-size: 18px;
            flex-shrink: 0;
        }

        /* Card Styles */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .card-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .card-header h2 {
            margin: 0;
            font-size: 20px;
            color: var(--text);
        }

        .card-body {
            display: grid;
            gap: 16px;
        }

        /* Grid */
        .grid {
            display: grid;
            gap: 20px;
        }

        .grid.cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid.cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        /* Forms */
        input, select, textarea {
            width: 100%;
            min-height: 40px;
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 10px 14px;
            font: inherit;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 0 18px;
            border: none;
            border-radius: 7px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: white;
            border-color: var(--primary);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        /* Table */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px 14px;
            text-align: center;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        th {
            background: var(--bg);
            font-weight: 600;
            color: var(--text);
        }

        tr:hover {
            background: var(--bg);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            body {
                grid-template-columns: 200px 1fr;
            }

            main {
                padding: 20px;
            }

            .grid.cols-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            body {
                grid-template-columns: 1fr;
                grid-template-rows: auto auto 1fr;
            }

            .sidebar {
                grid-column: 1;
                grid-row: 2;
                padding: 16px;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }

            .sidebar.active {
                max-height: 500px;
            }

            .sidebar-brand {
                margin-bottom: 16px;
            }

            .topbar {
                grid-column: 1;
                grid-row: 1;
                padding: 0 16px;
            }

            main {
                grid-column: 1;
                grid-row: 3;
                padding: 16px;
            }

            .grid.cols-2,
            .grid.cols-3 {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        main::-webkit-scrollbar {
            width: 8px;
        }

        main::-webkit-scrollbar-track {
            background: transparent;
        }

        main::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <a class="sidebar-brand" href="{{ route('dashboard') }}">
            <div style="width: 38px; height: 38px; border-radius: 10px; background: #ffffff; color: var(--primary, #0066D4); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 20px; font-weight: 800;">S</div>
            <span>My Asesmen</span>
        </a>

        <nav>
            <div class="sidebar-section">
                <div class="sidebar-section-title">Menu Utama</div>
                <ul class="sidebar-nav">
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                </ul>
            </div>

            @if (auth()->user()->role === 'administrator' || auth()->user()->role === 'teacher')
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Ujian</div>
                    <ul class="sidebar-nav">
                        @if(auth()->user()->role === 'teacher')
                            <li>
                                <a href="{{ route('exams.index') }}" class="{{ request()->routeIs('exams.*') ? 'active' : '' }}">
                                    <i class="fas fa-clipboard-list"></i> Ujian & Token
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('monitoring.index') }}" class="{{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                                    <i class="fas fa-chart-line"></i> Monitoring
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="fas fa-file-pdf"></i> Laporan
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            @if (auth()->user()->role === 'student')
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Ujian Saya</div>
                    <ul class="sidebar-nav">
                        <li>
                            <a href="{{ route('student.exams.index') }}" class="{{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
                                <i class="fas fa-book"></i> Daftar Ujian
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            @if (auth()->user()->role === 'administrator')
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Administrasi</div>
                    <ul class="sidebar-nav">
                        <li>
                            <a href="{{ route('admin.academic.index') }}" class="{{ request()->routeIs('admin.academic.*') ? 'active' : '' }}">
                                <i class="fas fa-school"></i> Data Akademik
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.teachers.index') }}" class="{{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                                <i class="fas fa-chalkboard-user"></i> Guru
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                                <i class="fas fa-users"></i> Siswa
                            </a>
                        </li>
                        <li>
                        </li>
                    </ul>
                </div>
            @endif

            <div class="sidebar-section" style="margin-top: auto; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;">
                <div class="sidebar-section-title">Akun</div>
                <ul class="sidebar-nav">
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                            @csrf
                            <button type="submit" style="width: 100%; background: none; border: none; color: inherit; text-align: left; cursor: pointer; padding: 0;">
                                <a style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: rgba(255, 255, 255, 0.8); text-decoration: none; font-weight: 500; transition: all 0.3s ease; border-left: 3px solid transparent;">
                                    <i class="fas fa-sign-out-alt"></i> Keluar
                                </a>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>

    <!-- Top Bar -->
    <header class="topbar">
        <div class="topbar-left">
            <h1 style="margin: 0; font-size: 18px; color: var(--text);">{{ $title ?? 'My Asssesmen' }}</h1>
        </div>
        <div class="topbar-right">
            <div class="user-menu" style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 6px 12px; border-radius: 8px; transition: background 0.3s ease;">
                @if(auth()->user()->role === 'teacher' && auth()->user()->gender)
                    <span style="font-weight: 500; color: var(--text);">{{ auth()->user()->gender === 'P' ? 'Halo Ibu' : 'Halo Bapak' }}, {{ auth()->user()->name }}</span>
                @else
                    <span style="font-weight: 500; color: var(--text);">Halo, {{ ucfirst(auth()->user()->role) }}</span>
                @endif
                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px;">
                    {{ auth()->user()->getInitials() }}
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Toast Container -->
        <div id="toast-container" style="position: fixed; top: 85px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; pointer-events: none;"></div>

        <!-- Global Delete Confirmation Modal -->
        <div id="globalDeleteModal" class="modal" style="display:none; z-index: 10000;">
            <div class="modal-content" style="max-width: 400px; text-align: center; padding: 32px 24px;">
                <img src="{{ asset('images/auth/delete-illustration.png') }}" alt="Konfirmasi Hapus" style="width: 140px; margin: 0 auto 16px;">
                <h3 style="margin-top: 0; font-size: 20px; font-weight: 700;">Hapus Data?</h3>
                <p id="globalDeleteMessage" style="color: #64748b; margin-bottom: 24px; font-size: 14px; line-height: 1.5;">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                
                <form id="globalDeleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div style="display: flex; justify-content: center; gap: 12px;">
                        <button type="button" class="btn" style="background: transparent; color: #64748b; border: 1px solid #cbd5e1;" onclick="closeGlobalDeleteModal()">Batal</button>
                        <button type="submit" class="btn btn-danger-light" style="background-color: #ef4444; color: white; border: none;">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>

        {{ $slot }}

        <!-- Global Footer -->
        <div class="global-footer">
            &copy; {{ date('Y') }} MyAsesmen. Dikembangkan oleh <strong>Yohanes Alvons</strong> &amp; <strong>Mario Hafner</strong>
        </div>
    </main>

    <!-- Global Modals -->
    <div id="modalBackdrop" class="modal-backdrop" style="display:none"></div>

n    <div id="updateModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>Perbarui Akun Guru</h3>
            <form id="updateForm" method="POST" style="display:flex;flex-direction:column;gap:10px;">
                @csrf
                @method('PUT')
                <label>Nama<input id="update_name" name="name" required></label>
                <label>Username<input id="update_username" name="username" required></label>
                <div style="display:flex;gap:10px;">
                    <label style="flex:1">NIP<input id="update_nip" name="nip"></label>
                    <label style="flex:1">Mapel<input id="update_mapel" name="mapel"></label>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:6px;">
                    <button type="button" id="cancelUpdate" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="resetModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width:420px;">
            <h3>Reset Password</h3>
            <p id="resetTargetName" class="text-muted"></p>
            <form id="resetForm" method="POST" style="display:flex;flex-direction:column;gap:10px;">
                @csrf
                <input id="reset_password" name="password" placeholder="Password baru" required>
                <div style="display:flex;justify-content:flex-end;gap:10px;">
                    <button type="button" id="cancelReset" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>



    <style>
        /* Modal common styles */
        .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:60}
        .modal{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;z-index:70}
        .modal-content{background:var(--surface);border-radius:12px;padding:20px;box-shadow:var(--shadow-lg);width:100%;max-width:640px}
        .modal h3{margin:0 0 8px;font-size:18px;color:var(--text)}
        .modal .text-muted{color:var(--text-muted);margin-bottom:12px}
        .modal input{padding:10px;border-radius:8px;border:1px solid var(--border);}
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
        const backdrop = document.getElementById('modalBackdrop');
        const updateModal = document.getElementById('updateModal');
        const resetModal = document.getElementById('resetModal');
        const updateForm = document.getElementById('updateForm');
        const resetForm = document.getElementById('resetForm');

        function openModal(modal){ backdrop.style.display='block'; modal.style.display='flex'; }
        function closeModal(modal){ modal.style.display='none'; backdrop.style.display='none'; }

        // Generic openers
        document.querySelectorAll('.open-update-modal').forEach(btn=>{
            btn.addEventListener('click', function(){
                const action = this.getAttribute('data-action');
                updateForm.setAttribute('action', action);
                document.getElementById('update_name').value = this.getAttribute('data-name') || '';
                document.getElementById('update_username').value = this.getAttribute('data-username') || '';
                document.getElementById('update_nip').value = this.getAttribute('data-nip') || '';
                document.getElementById('update_mapel').value = this.getAttribute('data-subject') || '';
                openModal(updateModal);
            });
        });

        document.querySelectorAll('.open-reset-modal').forEach(btn=>{
            btn.addEventListener('click', function(){
                const action = this.getAttribute('data-action');
                const name = this.getAttribute('data-name') || '';
                resetForm.setAttribute('action', action);
                document.getElementById('resetTargetName').textContent = name ? ('Akun: ' + name) : '';
                document.getElementById('reset_password').value = '';
                openModal(resetModal);
            });
        });

        document.querySelectorAll('.open-delete-modal').forEach(btn=>{
            btn.addEventListener('click', function(){
                const action = this.getAttribute('data-action');
                const name = this.getAttribute('data-name') || '';
                const message = name ? ('Apakah Anda yakin ingin menghapus ' + name + ' secara permanen?') : null;
                confirmGlobalDelete(action, message);
            });
        });

        document.getElementById('cancelUpdate').addEventListener('click', function(){ closeModal(updateModal); });
        document.getElementById('cancelReset').addEventListener('click', function(){ closeModal(resetModal); });

        // close when clicking backdrop
        backdrop.addEventListener('click', function(){ 
            if(updateModal) updateModal.style.display = 'none';
            if(resetModal) resetModal.style.display = 'none';
            backdrop.style.display='none'; 
            document.getElementById('globalDeleteModal').style.display='none';
        });

        // Global Delete Modal Logic
        function confirmGlobalDelete(actionUrl, customMessage = null) {
            const modal = document.getElementById('globalDeleteModal');
            const backdrop = document.getElementById('modalBackdrop');
            const form = document.getElementById('globalDeleteForm');
            const messageEl = document.getElementById('globalDeleteMessage');

            form.action = actionUrl;
            
            if(customMessage) {
                messageEl.innerText = customMessage;
            } else {
                messageEl.innerText = "Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.";
            }

            modal.style.display = 'flex';
            backdrop.style.display = 'block';
        }

        function closeGlobalDeleteModal() {
            const modal = document.getElementById('globalDeleteModal');
            const backdrop = document.getElementById('modalBackdrop');
            modal.style.display = 'none';
            backdrop.style.display = 'none';
        }
        
        // Toast Notification System
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            let bgColor, icon, textColor;
            
            if (type === 'error') {
                bgColor = '#fef2f2';
                textColor = '#ef4444';
                icon = '<i class="fas fa-exclamation-circle"></i>';
            } else {
                const lowerMsg = message.toLowerCase();
                if (lowerMsg.includes('dihapus') || lowerMsg.includes('hapus')) {
                    bgColor = '#fef2f2';
                    textColor = '#ef4444'; // Red
                    icon = '<i class="fas fa-trash-alt"></i>';
                } else if (lowerMsg.includes('diperbarui') || lowerMsg.includes('edit')) {
                    bgColor = '#eff6ff';
                    textColor = '#3b82f6'; // Blue
                    icon = '<i class="fas fa-info-circle"></i>';
                } else {
                    bgColor = '#f0fdf4';
                    textColor = '#22c55e'; // Green
                    icon = '<i class="fas fa-check-circle"></i>';
                }
            }

            toast.style.cssText = `
                background: ${bgColor};
                color: ${textColor};
                padding: 16px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 500;
                font-size: 14px;
                border-left: 4px solid ${textColor};
                pointer-events: auto;
                cursor: pointer;
                transform: translateX(100%);
                opacity: 0;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            `;

            toast.innerHTML = `${icon} <span>${message}</span>`;
            
            toast.onclick = () => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            };

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            }, 10);

            setTimeout(() => {
                if(toast.parentElement) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            @if(session('status'))
                showToast("{{ session('status') }}", "success");
            @endif

            @if($errors->any())
                showToast("{{ $errors->first() }}", "error");
            @endif
        });
    </script>
</body>
</html>
