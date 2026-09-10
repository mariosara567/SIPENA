<x-layouts.app :title="'Monitoring Ujian - My Asssesmen'">
    {{-- Status Filter Tabs --}}
    <section class="card" style="padding:16px 24px; border-top:5px solid var(--primary); margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap;">
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
        </div>
    </section>

    {{-- Exam List --}}
    <section class="card table-wrap">
        <h2 style="margin:0 0 16px; font-size:18px;">Daftar Ujian</h2>
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Ujian</th>
                    <th>Mapel</th>
                    <th>Status</th>
                    <th>Peserta</th>
                    <th>Mengerjakan</th>
                    <th>Selesai</th>
                    <th>Progress</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td style="text-align:left;">
                        <strong>{{ $row['exam']->title }}</strong>
                        <br><small style="color:var(--text-muted);">{{ $row['exam']->teacher->user->name }} · {{ optional($row['exam']->exam_date)->format('d M Y') }}</small>
                    </td>
                    <td>
                        {{ $row['exam']->subject->name }}
                        <br><small style="color:var(--text-muted); font-size:11px;">{{ $row['exam']->classes->pluck('display_name')->join(', ') }}</small>
                    </td>
                    <td>
                        <span class="status-badge {{ $row['exam']->status }}">{{ ucfirst($row['exam']->status) }}</span>
                    </td>
                    <td>{{ $row['exam']->participants_count }}</td>
                    <td>{{ $row['exam']->in_progress_count }}</td>
                    <td>{{ $row['exam']->completed_count }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px; justify-content:center;">
                            <div style="flex:1; max-width:80px; height:6px; background:var(--border); border-radius:99px; overflow:hidden;">
                                <div style="width:{{ min($row['progress'], 100) }}%; height:100%; background:{{ $row['progress'] >= 100 ? '#22c55e' : 'var(--primary)' }}; border-radius:99px; transition:width 0.3s;"></div>
                            </div>
                            <span style="font-size:12px; font-weight:700;">{{ $row['progress'] }}%</span>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('monitoring.show', $row['exam']) }}" class="btn btn-secondary" style="padding:0 12px; min-height:32px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                            <i class="fas fa-eye"></i> Live Monitoring
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="padding:32px; color:var(--text-muted);">Belum ada data ujian.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <script>
        // Auto refresh daftar ujian setiap 15 detik
        setTimeout(() => location.reload(), 15000);
    </script>
</x-layouts.app>
