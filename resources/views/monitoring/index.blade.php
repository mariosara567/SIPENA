<x-layouts.app :title="'Monitoring Ujian - My Asssesmen'">

    {{-- Status Filter Tabs --}}
    <section class="card" style="padding: 16px 24px; border-top: 5px solid var(--primary); margin-bottom: 0;">
        <div class="filter-pills">
            <a href="{{ route('monitoring.index', ['status' => 'all']) }}" class="{{ $statusFilter === 'all' ? 'active' : '' }}">
                Semua <span class="pill-count">{{ $statusCounts['all'] }}</span>
            </a>
            <a href="{{ route('monitoring.index', ['status' => 'active']) }}" class="{{ $statusFilter === 'active' ? 'active' : '' }}">
                <i class="fas fa-circle" style="font-size:8px; color:#22c55e;"></i> Aktif <span class="pill-count">{{ $statusCounts['active'] }}</span>
            </a>
            <a href="{{ route('monitoring.index', ['status' => 'scheduled']) }}" class="{{ $statusFilter === 'scheduled' ? 'active' : '' }}">
                <i class="fas fa-circle" style="font-size:8px; color:#3b82f6;"></i> Terjadwal <span class="pill-count">{{ $statusCounts['scheduled'] }}</span>
            </a>
            <a href="{{ route('monitoring.index', ['status' => 'closed']) }}" class="{{ $statusFilter === 'closed' ? 'active' : '' }}">
                <i class="fas fa-circle" style="font-size:8px; color:#94a3b8;"></i> Selesai <span class="pill-count">{{ $statusCounts['closed'] }}</span>
            </a>
        </div>
    </section>

    {{-- Exam List Table --}}
    <section class="card table-wrap" style="margin-top: 16px; padding: 0;">
        {{-- Table Header Bar --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--border); flex-wrap: wrap; gap: 12px;">
            <div>
                <h2 style="margin: 0 0 2px; font-size: 18px; font-weight: 700; color: var(--text);">Daftar Ujian</h2>
                <p style="margin: 0; font-size: 13px; color: #64748b;">{{ $rows->count() }} ujian ditampilkan</p>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="background: rgba(59,130,246,0.1); color: var(--primary); padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                    Total Peserta: {{ $total }}
                </span>
                <button onclick="location.reload()" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-sync-alt" id="refreshIcon"></i> Refresh
                </button>
            </div>
        </div>

        <table class="hero-table" style="margin: 0;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid var(--border);">
                    <th style="padding: 14px 16px 14px 24px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; white-space: nowrap;">Ujian</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Mapel / Kelas</th>
                    <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Status</th>
                    <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Peserta</th>
                    <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Mengerjakan</th>
                    <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Selesai</th>
                    <th style="padding: 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Progress</th>
                    <th style="padding: 14px 24px 14px 16px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding: 14px 16px 14px 24px; text-align: left;">
                        <div style="font-weight: 700; color: #1e293b; font-size: 14px; margin-bottom: 3px;">{{ $row['exam']->title }}</div>
                        <div style="font-size: 12px; color: #64748b;">
                            <i class="fas fa-user" style="margin-right: 4px; font-size: 10px;"></i>{{ $row['exam']->teacher->user->name }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-calendar" style="margin-right: 4px; font-size: 10px;"></i>{{ optional($row['exam']->exam_date)->format('d M Y') ?? '-' }}
                        </div>
                    </td>
                    <td style="padding: 14px 16px; text-align: left;">
                        <div style="font-weight: 600; color: #334155; font-size: 14px;">{{ $row['exam']->subject->name }}</div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">{{ $row['exam']->classes->pluck('display_name')->join(', ') ?: '-' }}</div>
                    </td>
                    <td style="padding: 14px 16px; text-align: center;">
                        <span class="status-badge {{ $row['exam']->status }}">{{ ucfirst($row['exam']->status) }}</span>
                    </td>
                    <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: #1e293b;">
                        {{ $row['exam']->participants_count }}
                    </td>
                    <td style="padding: 14px 16px; text-align: center;">
                        @if($row['exam']->in_progress_count > 0)
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #eff6ff; color: #2563eb; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 700;">
                                <span style="width: 6px; height: 6px; background: #2563eb; border-radius: 50%; animation: pulse 1.5s infinite;"></span>
                                {{ $row['exam']->in_progress_count }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-size: 13px;">0</span>
                        @endif
                    </td>
                    <td style="padding: 14px 16px; text-align: center;">
                        @if($row['exam']->completed_count > 0)
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #f0fdf4; color: #16a34a; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 700;">
                                <i class="fas fa-check" style="font-size: 10px;"></i>
                                {{ $row['exam']->completed_count }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-size: 13px;">0</span>
                        @endif
                    </td>
                    <td style="padding: 14px 16px; text-align: center;">
                        <div style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                            <div style="flex: 1; max-width: 90px; height: 6px; background: var(--border); border-radius: 99px; overflow: hidden;">
                                <div style="width: {{ min($row['progress'], 100) }}%; height: 100%; background: {{ $row['progress'] >= 100 ? '#22c55e' : 'var(--primary)' }}; border-radius: 99px; transition: width 0.3s;"></div>
                            </div>
                            <span style="font-size: 12px; font-weight: 700; color: {{ $row['progress'] >= 100 ? '#16a34a' : 'var(--primary)' }}; min-width: 32px;">{{ $row['progress'] }}%</span>
                        </div>
                    </td>
                    <td style="padding: 14px 24px 14px 16px; text-align: center;">
                        <a href="{{ route('monitoring.show', $row['exam']) }}" class="btn btn-secondary" style="padding: 0 14px; min-height: 34px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                            <i class="fas fa-eye"></i> Live Monitoring
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 48px 24px; text-align: center; color: var(--text-muted);">
                        <i class="fas fa-clipboard-list" style="font-size: 36px; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
                        Belum ada data ujian yang sesuai filter.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>

    <script>
        // Tombol refresh dengan animasi
        document.querySelector('[onclick="location.reload()"]')?.addEventListener('click', function() {
            const icon = document.getElementById('refreshIcon');
            if (icon) { icon.classList.add('fa-spin'); }
        });

        // Auto refresh setiap 10 detik (lebih cepat dari sebelumnya 15 detik)
        let autoRefreshTimer = setTimeout(() => location.reload(), 10000);

        // Reset timer jika user aktif di halaman
        document.addEventListener('mousemove', () => {
            clearTimeout(autoRefreshTimer);
            autoRefreshTimer = setTimeout(() => location.reload(), 10000);
        });
    </script>
</x-layouts.app>
