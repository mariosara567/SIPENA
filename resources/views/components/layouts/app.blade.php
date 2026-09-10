@props(['title' => 'My Asssesmen'])
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS / Tailwind -->
    <style>
        :root {
            /* Blue Brand Theme */--primary: #0066D4;
            --primary-light: #3b8cf2;
            --primary-dark: #004c9e;
            --secondary: #6366f1;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #ea580c;
            --bg: #F8F9FA;
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
            padding: 24px 0;
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
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border-left: none;
            margin: 2px 12px;
            border-radius: 10px;
        }

        .sidebar-nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .sidebar-nav a.active {
            background: #ffffff;
            color: #0066D4;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav i {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.15);
            font-size: 14px;
        }

        .sidebar-nav a.active i {
            background: rgba(0, 102, 212, 0.1);
            color: #0066D4;
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
            position: sticky;
            top: 0;
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
            padding: 36px 36px 0 36px;
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

        .stat-card.primary .stat-icon { background: rgba(0, 102, 212, 0.10); color: var(--primary); }
        .stat-card.success .stat-icon { background: rgba(22, 163, 74, 0.10); color: var(--success); }
        .stat-card.warning .stat-icon { background: rgba(234, 88, 12, 0.10); color: var(--warning); }

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
            box-shadow: 0 0 0 4px rgba(0, 102, 212, 0.1);
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
            padding: 10px 14px;
            text-align: center;
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
                grid-template-rows: auto auto auto;
            }

            .sidebar {
                grid-column: 1;
                grid-row: 2;
                padding: 16px;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                position: static;
                height: auto;
            }

            .sidebar.active {
                max-height: 500px;
            }

            .topbar {
                grid-column: 1;
                grid-row: 1;
                padding: 0 16px;
                position: sticky;
                top: 0;
            }

            main {
                grid-column: 1;
                grid-row: 3;
                padding: 16px;
                min-height: auto;
            }

            .grid.two {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar (now for the whole page, not main element) */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        /* ===== NEW: Filter Pills (Monitoring tabs) ===== */
        .filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .filter-pills a, .filter-pills button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: 2px solid var(--border);
            background: var(--surface);
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.25s ease;
            min-height: 38px;
        }
        .filter-pills a:hover, .filter-pills button:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        .filter-pills a.active, .filter-pills button.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .filter-pills .pill-count {
            font-size: 11px;
            background: rgba(255,255,255,0.25);
            padding: 2px 8px;
            border-radius: 99px;
            font-weight: 800;
        }
        .filter-pills a.active .pill-count {
            background: rgba(255,255,255,0.3);
        }

        /* ===== NEW: Status Badges ===== */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .status-badge.active { background: #dcfce7; color: #166534; }
        .status-badge.scheduled { background: #dbeafe; color: #1e40af; }
        .status-badge.closed { background: #f1f5f9; color: #64748b; }
        .status-badge::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }
        .status-badge.active::before { background: #22c55e; }
        .status-badge.scheduled::before { background: #3b82f6; }
        .status-badge.closed::before { background: #94a3b8; }

        /* ===== NEW: Class Chips / Tags ===== */
        .chip-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px 0;
            min-height: 40px;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(0, 102, 212, 0.08);
            color: var(--primary);
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(0, 102, 212, 0.2);
            transition: all 0.2s;
            cursor: default;
        }
        .chip .chip-remove {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(0, 102, 212, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            color: var(--primary);
        }
        .chip .chip-remove:hover {
            background: var(--danger);
            color: white;
        }

        /* Multi-select dropdown */
        .multi-select-wrapper {
            position: relative;
        }
        .multi-select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--surface);
            border: 2px solid var(--primary);
            border-radius: 10px;
            box-shadow: var(--shadow-lg);
            z-index: 200;
            max-height: 280px;
            overflow-y: auto;
            display: none;
        }
        .multi-select-dropdown.open { display: block; }
        .multi-select-group-title {
            padding: 8px 14px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
        }
        .multi-select-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            cursor: pointer;
            transition: background 0.15s;
            font-size: 14px;
            font-weight: 500;
        }
        .multi-select-option:hover { background: var(--bg); }
        .multi-select-option.selected { background: rgba(0, 102, 212, 0.06); color: var(--primary); font-weight: 600; }
        .multi-select-option.disabled { opacity: 0.35; pointer-events: none; }
        .multi-select-option input[type="checkbox"] {
            width: 18px;
            min-height: 18px;
            accent-color: var(--primary);
        }

        /* ===== NEW: Google Form Style Question Card ===== */
        .question-card {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 0;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: all 0.25s ease;
            overflow: hidden;
        }
        .question-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }
        .question-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 2px solid var(--border);
            background: var(--bg);
        }
        .question-card-header .q-number {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 15px;
            color: var(--primary);
        }
        .question-card-header .q-number span {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .question-card-header .q-actions {
            display: flex;
            gap: 6px;
        }
        .question-card-header .q-actions a,
        .question-card-header .q-actions button {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 13px;
            text-decoration: none;
            padding: 0;
        }
        .question-card-header .q-actions a:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .question-card-header .q-actions button:hover {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }
        .question-card-body {
            padding: 20px;
        }
        .question-card-body .q-text {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.7;
            margin-bottom: 16px;
        }
        .question-card-body .q-image {
            max-width: 320px;
            max-height: 200px;
            object-fit: contain;
            border-radius: 10px;
            border: 2px solid var(--border);
            margin-bottom: 16px;
        }
        .question-options {
            display: grid;
            gap: 8px;
        }
        .question-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            background: var(--bg);
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        .question-option.correct {
            background: #dcfce7;
            border-color: #22c55e;
            color: #166534;
            font-weight: 700;
        }
        .question-option .opt-letter {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--surface);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .question-option.correct .opt-letter {
            background: #22c55e;
            color: white;
            border-color: #22c55e;
        }
        .question-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            border-top: 1px solid var(--border);
            background: var(--bg);
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ===== NEW: Collapsible Import Section ===== */
        .import-section {
            background: var(--surface);
            border: 2px solid var(--border);
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .import-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            cursor: pointer;
            transition: background 0.2s;
            gap: 12px;
        }
        .import-section-header:hover { background: var(--bg); }
        .import-section-header .import-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }
        .import-section-header .import-title i {
            color: var(--primary);
        }
        .import-section-header .chevron {
            transition: transform 0.3s;
            color: var(--text-muted);
        }
        .import-section-header .chevron.open { transform: rotate(180deg); }
        .import-section-body {
            display: none;
            padding: 0 20px 16px;
            border-top: 1px solid var(--border);
        }
        .import-section-body.open {
            display: block;
            padding-top: 16px;
        }

        /* ===== NEW: Participants Modal ===== */
        .participants-modal .modal-content {
            max-width: 700px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
        }
        .participants-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border);
            margin-bottom: 16px;
        }
        .participants-modal .modal-header h3 {
            margin: 0;
            font-size: 18px;
        }
        .participants-modal .modal-body {
            overflow-y: auto;
            flex: 1;
        }
        .participants-modal .close-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--text-muted);
            transition: all 0.2s;
        }
        .participants-modal .close-btn:hover {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }

        /* ===== NEW: Google Form-style input (question editor) ===== */
        .gform-card {
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .gform-card.active {
            border-left: 5px solid var(--primary);
        }
        .gform-card-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 16px 24px;
        }
        .gform-card-header h2 {
            color: white;
            margin: 0;
            font-size: 16px;
        }
        .gform-card-header p {
            color: rgba(255,255,255,0.8);
            margin: 4px 0 0;
            font-size: 13px;
        }
        .gform-card-body {
            padding: 24px;
        }
        .gform-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
        }
        .gform-option input[type="radio"] {
            width: 20px;
            min-height: 20px;
            accent-color: var(--primary);
            cursor: pointer;
            flex-shrink: 0;
        }
        .gform-option input[type="text"] {
            flex: 1;
            min-height: 42px;
            border: none;
            border-bottom: 2px solid var(--border);
            border-radius: 0;
            padding: 8px 4px;
            font-size: 15px;
            transition: border-color 0.2s;
        }
        .gform-option input[type="text"]:focus {
            border-bottom-color: var(--primary);
            box-shadow: none;
            outline: none;
        }
        .gform-option .opt-label {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--bg);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        /* ===== Fix: 100% zoom layout ===== */
        .card, .grid, main, .topbar, section, article {
            min-width: 0;
        }
        .grid.two > * {
            min-width: 0;
        }

        /* Token display */
        .token-display {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 4px;
            color: var(--primary);
            background: rgba(0, 102, 212, 0.06);
            padding: 8px 16px;
            border-radius: 8px;
            border: 2px dashed rgba(0, 102, 212, 0.3);
            display: inline-block;
        }

        /* Compact info grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .info-item .info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }
        .info-item .info-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }
        /* ── HeroUI Table Styles ── */
        .table-wrap {
            background: #ffffff;
            border-radius: 14px;
            padding: 0;
            overflow-x: auto;
            overflow-y: hidden;
            border: 1px solid #e2e8f0;
        }
        .hero-table {
            width: 100%;
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            font-size: 14px;
        }
        .hero-table thead th {
            background: #f8fafc !important;
            padding: 14px 16px !important;
            color: #475569 !important;
            font-weight: 600 !important;
            position: relative;
            border: none !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            font-size: 13px !important;
        }
        .hero-table thead th:not(:last-child)::after {
            content: "";
            position: absolute;
            right: 0;
            top: 30%;
            height: 40%;
            width: 1px;
            background: #d4d4d8;
        }
        .hero-table tbody tr {
            transition: background 0.15s;
        }
        .hero-table tbody td {
            background: #ffffff !important;
            padding: 14px 16px !important;
            color: #3f3f46 !important;
            border: none !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .hero-table tbody tr:nth-child(even) td {
            background: #fafafa !important;
        }
        .hero-table tbody tr:hover td {
            background: #f8fafc !important;
        }
        .hero-table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            body {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                bottom: 0;
                width: 280px;
                transition: left 0.3s ease;
                z-index: 1000;
            }
            .sidebar.open {
                left: 0;
            }
            .topbar {
                grid-column: 1 / -1;
                padding: 0 16px;
            }
            main {
                grid-column: 1 / -1;
                padding: 20px 16px 0 16px;
            }
            .user-menu span {
                display: none;
            }
            .topbar-left h1 {
                font-size: 18px;
            }
            .dashboard-stats {
                flex-direction: column;
            }
            .mobile-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                backdrop-filter: blur(2px);
            }
            .mobile-overlay.open {
                display: block;
            }
            .btn-mobile-toggle {
                display: inline-flex !important;
            }
            .card {
                padding: 16px;
            }
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

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="mobile-overlay"></div>

    <!-- Top Bar -->
    <header class="topbar">
        <div class="topbar-left">
            <button id="mobileToggleBtn" class="btn-mobile-toggle" style="display: none; background: transparent; border: none; font-size: 20px; color: var(--text); cursor: pointer; margin-right: 12px; padding: 4px;">
                <i class="fas fa-bars"></i>
            </button>
            <h1>{{ $title }}</h1>
        </div>
        <div class="topbar-right">
            <div class="user-menu">
                @if(auth()->user()->role === 'teacher' && auth()->user()->gender)
                    <span>{{ auth()->user()->gender === 'P' ? 'Halo Ibu' : 'Halo Bapak' }}, {{ auth()->user()->name }}</span>
                @else
                    <span>Halo, {{ ucfirst(auth()->user()->role) }}</span>
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
        function openModal(modalId) {
            const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
            const backdrop = document.getElementById('modalBackdrop');
            if (modal && backdrop) {
                modal.style.display = 'flex';
                backdrop.style.display = 'block';
            }
        }

        function closeModal(modalId) {
            const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
            const backdrop = document.getElementById('modalBackdrop');
            if (modal && backdrop) {
                modal.style.display = 'none';
                backdrop.style.display = 'none';
            }
        }

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

        document.addEventListener('DOMContentLoaded', function(){
            const updateModal = document.getElementById('updateModal');
            const resetModal = document.getElementById('resetModal');
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            const updateForm = document.getElementById('updateForm');
            const resetForm = document.getElementById('resetForm');

            @if(session('status'))
                showToast("{{ session('status') }}", "success");
            @endif

            @if($errors->any())
                showToast("{{ $errors->first() }}", "error");
            @endif

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

            const backdrop = document.getElementById('modalBackdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function(){
                    [updateModal, resetModal, deleteModal].forEach(m => {
                        if (m) m.style.display = 'none';
                    });
                    backdrop.style.display = 'none';
                });
            }

            // Mobile Sidebar Toggle
            const mobileToggleBtn = document.getElementById('mobileToggleBtn');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const sidebar = document.querySelector('.sidebar');

            if (mobileToggleBtn && mobileOverlay && sidebar) {
                mobileToggleBtn.addEventListener('click', function() {
                    sidebar.classList.add('open');
                    mobileOverlay.classList.add('open');
                });

                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    mobileOverlay.classList.remove('open');
                });
            }
        });
    </script>
</body>
</html>
