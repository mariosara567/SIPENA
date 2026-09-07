@props(['title' => 'My Asssesmen'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0f766e;
            --primary-light: #14b8a6;
            --primary-dark: #0d5d56;
            --secondary: #6366f1;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #ea580c;
            --bg: #f5f7fa;
            --surface: #ffffff;
            --text: #0f172a;
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

        html, body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            height: 100%;
            font-weight: 500;
        }

        body {
            display: grid;
            grid-template-columns: 270px 1fr;
            grid-template-rows: 75px 1fr;
        }

        /* Sidebar */
        .sidebar {
            grid-row: 1 / -1;
            grid-column: 1;
            background: linear-gradient(135deg, var(--primary) 0%, #0d5d56 100%);
            color: white;
            padding: 24px 0;
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(15, 118, 110, 0.25);
            z-index: 100;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0 24px;
            margin-bottom: 40px;
            text-decoration: none;
            color: white;
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -0.5px;
        }

        .sidebar-brand .mark {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 20px;
        }

        .sidebar-section {
            margin-bottom: 28px;
        }

        .sidebar-section-title {
            padding: 0 24px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 12px;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 24px;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            margin: 0 8px;
            border-radius: 0 8px 8px 0;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.2);
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
            padding: 0 36px;
            background: var(--surface);
            border-bottom: 2px solid var(--border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            z-index: 50;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 24px;
            flex: 1;
        }

        .topbar-left h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 10px;
            background: var(--bg);
            color: var(--primary);
            font-weight: 700;
            font-size: 13px;
        }

        .logout-btn {
            min-height: 40px;
            padding: 0 18px;
            border: 2px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            color: var(--text);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
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
            overflow-y: auto;
            padding: 36px;
            background: var(--bg);
        }

        /* Alert Messages */
        .alert {
            padding: 16px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            border-left: 5px solid;
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 600;
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
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 28px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-lg);
        }

        .card h1, .card h2 {
            margin: 0 0 8px 0;
            color: var(--text);
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .card h1 {
            font-size: 24px;
        }

        .card h2 {
            font-size: 20px;
        }

        .card p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .card-header {
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 2px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            display: grid;
            gap: 18px;
        }

        /* Header & Dashboard styles */
        .header-section {
            margin-bottom: 40px;
        }

        .header-section h1 {
            margin: 0 0 8px 0;
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .header-section p {
            margin: 0;
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.6;
            font-weight: 500;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            background: var(--bg);
            border-radius: 50%;
            opacity: 0.5;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .stat-card.primary { border-top: 4px solid var(--primary); }
        .stat-card.success { border-top: 4px solid var(--success); }
        .stat-card.warning { border-top: 4px solid var(--warning); }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .stat-card.primary .stat-icon { background: rgba(15, 118, 110, 0.12); color: var(--primary); }
        .stat-card.success .stat-icon { background: rgba(22, 163, 74, 0.12); color: var(--success); }
        .stat-card.warning .stat-icon { background: rgba(234, 88, 12, 0.12); color: var(--warning); }

        .stat-content { position: relative; z-index: 1; }
        .stat-value { display: flex; align-items: baseline; gap: 8px; margin-bottom: 6px; }
        .stat-number { font-size: 32px; font-weight: 900; color: var(--text); letter-spacing: -0.5px; }
        .stat-label { font-size: 13px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-description { font-size: 13px; color: var(--text-muted); margin-top: 8px; font-weight: 500; }

        /* Modules Grid */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .module-card {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 28px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }

        .module-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); border-color: var(--primary); }

        .module-icon { width: 64px; height: 64px; border-radius: 14px; background: linear-gradient(135deg, var(--primary-light), var(--primary)); color: white; display: flex; align-items: center; justify-content: center; font-size: 30px; margin-bottom: 18px; }
        .module-title { font-size: 18px; font-weight: 800; margin-bottom: 8px; color: var(--text); letter-spacing: -0.3px; }
        .module-description { font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 18px; font-weight: 500; }
        .module-footer { display: flex; align-items: center; gap: 10px; color: var(--primary); font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.3px; }


        /* Grid */
        .grid {
            display: grid;
            gap: 18px;
        }

        .grid.two {
            grid-template-columns: repeat(2, 1fr);
        }

        /* Forms */
        input, select, textarea {
            width: 100%;
            min-height: 44px;
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 11px 16px;
            font: inherit;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.1);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        /* Buttons */
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 44px;
            padding: 0 22px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
            background: var(--primary);
            color: white;
            letter-spacing: -0.3px;
        }

        .button:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .button.secondary {
            background: var(--bg);
            color: var(--primary);
            border: 2px solid var(--border);
            font-weight: 700;
        }

        .button.secondary:hover {
            background: white;
            border-color: var(--primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 44px;
            padding: 0 22px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
            letter-spacing: -0.3px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--primary);
            border: 2px solid var(--border);
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
            padding: 14px 16px;
            text-align: left;
            border-bottom: 2px solid var(--border);
            font-size: 14px;
        }

        th {
            background: var(--bg);
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.3px;
        }

        tr:hover {
            background: var(--bg);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            body {
                grid-template-columns: 220px 1fr;
            }

            main {
                padding: 24px;
            }

            .grid.two {
                grid-template-columns: 1fr;
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

            .grid.two {
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
            <span class="mark">S</span>
            <span>My Asssesmen</span>
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
                        <li>
                            <a href="{{ route('exams.index') }}" class="{{ request()->routeIs('exams.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list"></i> {{ auth()->user()->role === 'teacher' ? 'Ujian & Token' : 'Kelola Ujian' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('monitoring.index') }}" class="{{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                                <i class="fas fa-chart-line"></i> Monitoring
                            </a>
                        </li>
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

            <div class="sidebar-section" style="margin-top: auto; border-top: 1px solid rgba(255, 255, 255, 0.15); padding-top: 24px;">
                <div class="sidebar-section-title">Akun</div>
                <ul class="sidebar-nav">
                    @if(auth()->user()->role === 'teacher')
                        <li><a href="{{ route('teacher.profile.edit') }}"><i class="fas fa-user-cog"></i> Profil Saya</a></li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                            @csrf
                            <button type="submit" style="width: 100%; background: none; border: none; color: inherit; text-align: left; cursor: pointer; padding: 0;">
                                <a style="display: flex; align-items: center; gap: 14px; padding: 13px 24px; color: rgba(255, 255, 255, 0.75); text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.3s ease; border-left: 3px solid transparent; margin: 0 8px; border-radius: 0 8px 8px 0;">
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
            <h1>{{ $title }}</h1>
        </div>
        <div class="topbar-right">
            <div class="user-menu">
                <i class="fas fa-user-circle" style="font-size: 22px;"></i>
                <span>{{ auth()->user()->name }}</span>
                <span style="font-size: 11px; color: rgba(15, 118, 110, 0.7); font-weight: 600;">({{ ucfirst(auth()->user()->role) }})</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @if (session('status'))
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Global Modals -->
    <div id="modalBackdrop" class="modal-backdrop" style="display:none"></div>

    <div id="updateModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>Perbarui Akun</h3>
            <form id="updateForm" method="POST" style="display:flex;flex-direction:column;gap:14px;">
                @csrf
                @method('PUT')
                <label>Nama<input id="update_name" name="name" required></label>
                <label>Username<input id="update_username" name="username" required></label>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <label style="flex:1;min-width:180px;">NIP<input id="update_nip" name="nip"></label>
                    <label style="flex:1;min-width:180px;">Mapel<input id="update_subject" name="subject"></label>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;">
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
            <form id="resetForm" method="POST" style="display:flex;flex-direction:column;gap:14px;">
                @csrf
                <input id="reset_password" name="password" placeholder="Password baru" required>
                <div style="display:flex;justify-content:flex-end;gap:10px;">
                    <button type="button" id="cancelReset" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width:520px;">
            <h3>Konfirmasi Hapus</h3>
            <p class="text-muted">Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan.</p>
            <p id="deleteTargetName" style="font-weight:600;margin:0 0 12px;color:var(--text);"></p>
            <div style="display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" id="cancelDelete" class="btn btn-secondary">Batal</button>
                <button type="button" id="confirmDelete" class="btn btn-danger">Hapus</button>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <style>
        .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:60}
        .modal{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;z-index:70}
        .modal-content{background:var(--surface);border-radius:14px;padding:24px;box-shadow:var(--shadow-lg);width:100%;max-width:640px;border:1px solid rgba(15,118,110,0.1);}
        .modal h3{margin:0 0 10px;font-size:20px;color:var(--text);}
        .modal .text-muted{color:var(--text-muted);margin-bottom:14px;}
        .modal input{padding:12px;border-radius:10px;border:1px solid var(--border);width:100%;}
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const backdrop = document.getElementById('modalBackdrop');
            const updateModal = document.getElementById('updateModal');
            const resetModal = document.getElementById('resetModal');
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const updateForm = document.getElementById('updateForm');
            const resetForm = document.getElementById('resetForm');

            function openModal(modal){ backdrop.style.display = 'block'; modal.style.display = 'flex'; }
            function closeModal(modal){ modal.style.display = 'none'; backdrop.style.display = 'none'; }

            document.querySelectorAll('.open-update-modal').forEach(btn=>{
                btn.addEventListener('click', function(){
                    const action = this.getAttribute('data-action');
                    updateForm.setAttribute('action', action);
                    document.getElementById('update_name').value = this.getAttribute('data-name') || '';
                    document.getElementById('update_username').value = this.getAttribute('data-username') || '';
                    document.getElementById('update_nip').value = this.getAttribute('data-nip') || '';
                    document.getElementById('update_subject').value = this.getAttribute('data-subject') || '';
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
                    deleteForm.setAttribute('action', action);
                    document.getElementById('deleteTargetName').textContent = name ? ('Akun: ' + name) : '';
                    openModal(deleteModal);
                });
            });

            document.getElementById('cancelUpdate').addEventListener('click', function(){ closeModal(updateModal); });
            document.getElementById('cancelReset').addEventListener('click', function(){ closeModal(resetModal); });
            document.getElementById('cancelDelete').addEventListener('click', function(){ closeModal(deleteModal); });
            document.getElementById('confirmDelete').addEventListener('click', function(){ deleteForm.submit(); });

            backdrop.addEventListener('click', function(){
                [updateModal, resetModal, deleteModal].forEach(m => m.style.display = 'none');
                backdrop.style.display = 'none';
            });
        });
    </script>
</body>
</html>
