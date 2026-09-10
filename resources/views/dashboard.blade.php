<x-layouts.app :title="'Beranda Utama'">

    <!-- Stats Cards -->
    <style>
        .dashboard-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .dash-card {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            min-width: 180px;
            transition: all 0.3s ease;
        }

        .dash-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .dash-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .dash-card.card-blue .dash-card-icon { background: rgba(0, 102, 212, 0.10); color: #0066D4; }
        .dash-card.card-red .dash-card-icon { background: rgba(220, 38, 38, 0.10); color: #dc2626; }
        .dash-card.card-green .dash-card-icon { background: rgba(22, 163, 74, 0.10); color: #16a34a; }
        .dash-card.card-teal .dash-card-icon { background: rgba(15, 118, 110, 0.10); color: #0f766e; }

        .dash-card-info {
            display: flex;
            flex-direction: column;
        }

        .dash-card-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            line-height: 1.3;
            margin-bottom: 2px;
        }

        .dash-card-number {
            font-size: 32px;
            font-weight: 900;
            color: var(--text);
            line-height: 1.1;
        }

        /* Welcome message card */
        .welcome-card {
            background: var(--surface);
            border: 2px dashed var(--border);
            border-radius: 14px;
            padding: 32px;
            text-align: center;
        }

        .welcome-card-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .welcome-card-title i {
            color: var(--primary);
            font-size: 20px;
        }

        .welcome-card-desc {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
        }


    </style>

    <div class="dashboard-stats">
        @php
            $cardStyles = ['card-blue', 'card-red', 'card-green', 'card-teal'];
        @endphp
        @foreach(array_slice($module['items'], 0, 4) as $index => $item)
            <div class="dash-card {{ $cardStyles[$index] ?? 'card-blue' }}">
                <div class="dash-card-icon">
                    <i class="{{ $item['icon'] ?? 'fas fa-chart-simple' }}"></i>
                </div>
                <div class="dash-card-info">
                    <div class="dash-card-label">{{ $item['name'] ?? 'Item' }}</div>
                    <div class="dash-card-number">{{ $item['count'] ?? 0 }}</div>
                </div>
            </div>
        @endforeach
    </div>

    @if(isset($teacherSubjects))
        <h3 style="font-size: 16px; font-weight: 700; color: #475569; margin: 10px 0 16px;">Mata Pelajaran & Kelas Yang Anda Ampu</h3>
        @if(count($teacherSubjects) > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
                @foreach($teacherSubjects as $subject)
                    <div style="background: var(--surface); border: 1.5px solid var(--border); border-radius: 12px; padding: 20px; transition: all 0.2s ease;">
                        @if(strtolower($subject['subject_group']) === 'wajib')
                            <span style="display: inline-block; padding: 4px 12px; background: #eff6ff; color: #2563eb; border-radius: 6px; font-size: 12px; font-weight: 600; margin-bottom: 12px;">Wajib</span>
                        @else
                            <span style="display: inline-block; padding: 4px 12px; background: #fef2f2; color: #dc2626; border-radius: 6px; font-size: 12px; font-weight: 600; margin-bottom: 12px;">Pilihan</span>
                        @endif
                        <div style="font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 8px;">{{ $subject['subject_name'] }}</div>
                        <div style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">
                            @if(count($subject['classes']) > 0)
                                {{ implode(', ', $subject['classes']) }}
                            @else
                                Belum ada kelas
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="background: var(--surface); border: 2px dashed var(--border); border-radius: 14px; padding: 32px; text-align: center;">
                <i class="fas fa-book" style="font-size: 32px; color: #cbd5e1; margin-bottom: 12px;"></i>
                <div style="font-size: 16px; font-weight: 600; color: #64748b;">Belum ada mata pelajaran yang diampu.</div>
                <p style="font-size: 14px; color: #94a3b8; margin-top: 8px;">Mata pelajaran akan muncul saat Anda membuat paket ujian untuk mata pelajaran tersebut.</p>
            </div>
        @endif
    @elseif(auth()->user()->isAdministrator())
        <div class="welcome-card">
            <div class="welcome-card-title">
                <i class="fas fa-info-circle"></i>
                Selamat Datang Kembali
            </div>
            <p class="welcome-card-desc">Silakan pilih sub-menu di samping untuk mengelola data sekolah dan kelengkapan ujian.</p>
        </div>
    @endif



</x-layouts.app>
