<x-layouts.app :title="'Live Monitoring - '.$exam->title">
    <a href="{{ route('monitoring.index') }}" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; color:#64748b; font-weight:600; font-size:15px; margin-bottom:16px; transition:color 0.2s;">
        <span style="display:flex; align-items:center; justify-content:center; width:24px; height:24px; background:var(--primary); color:white; border-radius:50%; font-size:12px;">
            <i class="fas fa-arrow-left"></i>
        </span>
        Kembali ke Daftar Ujian
    </a>

    {{-- Top Block: Konfigurasi & Token --}}
    <section class="card" style="display:flex; flex-wrap:wrap; gap:20px; align-items:stretch; padding:0; overflow:hidden;">
        {{-- Left: Config --}}
        <div style="flex:1; padding:20px 24px; border-right:1px solid var(--border); min-width:300px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                <div style="width:4px; height:16px; background:var(--primary); border-radius:4px;"></div>
                <h2 style="margin:0; font-size:13px; font-weight:700; color:#64748b; letter-spacing:0.5px;">KONFIGURASI KELAS & MATAPELAJARAN</h2>
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <div style="padding:10px 14px; border:1px solid var(--border); border-radius:6px; background:var(--surface); font-weight:600; font-size:14px; color:var(--text);">
                        {{ $exam->subject->name }}
                    </div>
                </div>
                <div>
                    <div style="padding:10px 14px; border:1px solid var(--border); border-radius:6px; background:var(--surface); font-weight:600; font-size:14px; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $exam->classes->pluck('display_name')->join(', ') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Token --}}
        <div style="padding:20px 24px; min-width:300px; display:flex; flex-direction:column; justify-content:center;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                <div style="width:8px; height:8px; background:#22c55e; border-radius:50%;"></div>
                <h2 style="margin:0; font-size:12px; font-weight:700; color:#64748b; letter-spacing:0.5px;">TOKEN UTAMA AKTIF</h2>
            </div>
            <div style="display:flex; align-items:center; justify-content:space-between; gap:16px;">
                <div id="active-token" style="font-size:32px; font-weight:900; letter-spacing:4px; color:var(--text); font-family:monospace;">
                    {{ $exam->token }}
                </div>
                <button type="button" class="btn" onclick="regenerateToken({{ $exam->id }})" style="font-size:13px; font-weight:700; padding:0 16px; min-height:40px; background:#f8fafc; border:1px solid var(--border); color:var(--text);">
                    <i class="fas fa-sync-alt" style="margin-right:6px;"></i> Acak Baru
                </button>
            </div>
        </div>
    </section>

    {{-- Bottom Block: Live Monitoring Table --}}
    <section class="card table-wrap" style="padding:0; overflow:hidden;">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; background:#f8fafc;">
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:4px; height:16px; background:#22c55e; border-radius:4px;"></div>
                <h2 style="margin:0; font-size:14px; font-weight:700; color:var(--text); letter-spacing:0.5px;">LIVE MONITORING PROGRESS MURID</h2>
            </div>
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <span class="status-badge active" style="font-size:11px; display:inline-flex; align-items:center; gap:5px;">
                    <span style="width:6px; height:6px; background:#22c55e; border-radius:50%; animation:pulse 1.5s infinite;"></span>
                    Live sync 4s
                </span>
                <div style="background:#eff6ff; color:#1d4ed8; font-size:13px; font-weight:700; padding:6px 14px; border-radius:99px; border:1px solid #bfdbfe;">
                    Total Terdaftar: <span id="total-count">{{ $participants->count() }}</span> Murid
                </div>
                <button type="button" class="btn btn-secondary" onclick="fetchLiveMonitoring(true)" style="padding:6px 14px; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                    <i class="fas fa-sync-alt" id="refreshIcon"></i> Refresh Sekarang
                </button>
            </div>
        </div>
        <table style="border:none; margin:0;" class="hero-table">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid var(--border);">
                    <th style="padding-left:24px; text-align:center; width:60px;">NO</th>
                    <th style="text-align:left;">NAMA MURID</th>
                    <th>NISN</th>
                    <th>STATUS</th>
                    <th style="padding-right:24px;">LOG TINDAKAN</th>
                </tr>
            </thead>
            <tbody id="monitoring-tbody">
                @forelse($participants as $index => $participant)
                    <tr style="border-bottom:1px solid var(--border);" id="participant-row-{{ $participant->id }}">
                        <td style="padding-left:24px; text-align:center; color:#94a3b8; font-weight:500;">
                            {{ $index + 1 }}
                        </td>
                        <td style="text-align:left; font-weight:600; color:var(--text);">
                            {{ $participant->student->user->name }}
                        </td>
                        <td style="color:#64748b;">
                            {{ $participant->student->nisn }}
                        </td>
                        <td style="font-weight:600;">
                            @if($participant->is_locked)
                                <div style="display:inline-flex; align-items:center; gap:6px; color:#ef4444; background:#fef2f2; padding:4px 10px; border-radius:99px; font-size:12px; border:1px solid #fecaca;">
                                    <div style="width:6px; height:6px; background:#ef4444; border-radius:50%;"></div>
                                    Keluar Tab (Lock)
                                </div>
                            @elseif($participant->finished_at)
                                <div style="display:inline-flex; align-items:center; gap:6px; color:#16a34a; background:#f0fdf4; padding:4px 10px; border-radius:99px; font-size:12px; border:1px solid #bbf7d0;">
                                    <div style="width:6px; height:6px; background:#16a34a; border-radius:50%;"></div>
                                    Selesai ({{ $participant->finished_at->format('H:i') }})
                                </div>
                            @elseif($participant->started_at)
                                <div style="display:inline-flex; align-items:center; gap:6px; color:#2563eb; background:#eff6ff; padding:4px 10px; border-radius:99px; font-size:12px; border:1px solid #bfdbfe;">
                                    <div style="width:6px; height:6px; background:#2563eb; border-radius:50%;"></div>
                                    Mengerjakan
                                </div>
                            @else
                                <span style="color:#94a3b8; font-size:13px; font-weight:500;">Belum Login</span>
                            @endif
                        </td>
                        <td style="padding-right:24px;">
                            @if($participant->is_locked)
                                <button class="btn" type="button" onclick="unlockParticipant({{ $participant->id }}, '{{ route('monitoring.unlock', $participant) }}')" style="background:#f59e0b; color:#fff; font-weight:700; font-size:12px; padding:0 14px; min-height:30px; border:none; cursor:pointer;">
                                    Buka Kunci Sesi
                                </button>
                            @else
                                <span style="color:#cbd5e1;">-</span>
                                @if($participant->violation_count > 0)
                                    <br><small style="color:#ef4444; font-size:11px; font-weight:600;">(Pernah dikunci: {{ $participant->violation_count }}x)</small>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:48px 24px; text-align:center; color:var(--text-muted);">
                            Belum ada murid yang didaftarkan untuk ujian ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }
    </style>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Fetch Live Monitoring via AJAX (polling cepat tiap 4 detik)
        let isFetching = false;
        async function fetchLiveMonitoring(isManual = false) {
            if (isFetching) return;
            isFetching = true;

            const icon = document.getElementById('refreshIcon');
            if (icon && isManual) icon.classList.add('fa-spin');

            try {
                const res = await fetch(window.location.href, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (res.ok) {
                    const data = await res.json();
                    if (data.token) {
                        const tokenEl = document.getElementById('active-token');
                        if (tokenEl && tokenEl.textContent.trim() !== data.token) {
                            tokenEl.textContent = data.token;
                        }
                    }
                    if (data.total !== undefined) {
                        const totalEl = document.getElementById('total-count');
                        if (totalEl) totalEl.textContent = data.total;
                    }
                    if (Array.isArray(data.participants)) {
                        renderParticipantsTable(data.participants);
                    }
                }
            } catch (err) {
                console.warn('Live monitoring poll error:', err);
            } finally {
                isFetching = false;
                if (icon) icon.classList.remove('fa-spin');
            }
        }

        function renderParticipantsTable(participants) {
            const tbody = document.getElementById('monitoring-tbody');
            if (!tbody) return;

            if (participants.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" style="padding:48px 24px; text-align:center; color:var(--text-muted);">Belum ada murid yang didaftarkan untuk ujian ini.</td></tr>`;
                return;
            }

            let html = '';
            participants.forEach((p, idx) => {
                let statusHtml = '';
                if (p.is_locked) {
                    statusHtml = `<div style="display:inline-flex; align-items:center; gap:6px; color:#ef4444; background:#fef2f2; padding:4px 10px; border-radius:99px; font-size:12px; border:1px solid #fecaca;"><div style="width:6px; height:6px; background:#ef4444; border-radius:50%;"></div>Keluar Tab (Lock)</div>`;
                } else if (p.finished_at) {
                    statusHtml = `<div style="display:inline-flex; align-items:center; gap:6px; color:#16a34a; background:#f0fdf4; padding:4px 10px; border-radius:99px; font-size:12px; border:1px solid #bbf7d0;"><div style="width:6px; height:6px; background:#16a34a; border-radius:50%;"></div>Selesai (${p.finished_at})</div>`;
                } else if (p.started_at) {
                    statusHtml = `<div style="display:inline-flex; align-items:center; gap:6px; color:#2563eb; background:#eff6ff; padding:4px 10px; border-radius:99px; font-size:12px; border:1px solid #bfdbfe;"><div style="width:6px; height:6px; background:#2563eb; border-radius:50%;"></div>Mengerjakan</div>`;
                } else {
                    statusHtml = `<span style="color:#94a3b8; font-size:13px; font-weight:500;">Belum Login</span>`;
                }

                let actionHtml = '';
                if (p.is_locked) {
                    actionHtml = `<button class="btn" type="button" onclick="unlockParticipant(${p.id}, '${p.unlock_url}')" style="background:#f59e0b; color:#fff; font-weight:700; font-size:12px; padding:0 14px; min-height:30px; border:none; cursor:pointer;">Buka Kunci Sesi</button>`;
                } else {
                    actionHtml = `<span style="color:#cbd5e1;">-</span>`;
                    if (p.violation_count > 0) {
                        actionHtml += `<br><small style="color:#ef4444; font-size:11px; font-weight:600;">(Pernah dikunci: ${p.violation_count}x)</small>`;
                    }
                }

                html += `
                    <tr style="border-bottom:1px solid var(--border);" id="participant-row-${p.id}">
                        <td style="padding-left:24px; text-align:center; color:#94a3b8; font-weight:500;">${idx + 1}</td>
                        <td style="text-align:left; font-weight:600; color:var(--text);">${escapeHtml(p.name)}</td>
                        <td style="color:#64748b;">${escapeHtml(p.nisn)}</td>
                        <td style="font-weight:600;">${statusHtml}</td>
                        <td style="padding-right:24px;">${actionHtml}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function escapeHtml(str) {
            return String(str || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]);
        }

        // AJAX Unlock participant
        async function unlockParticipant(id, url) {
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                if (res.ok) {
                    fetchLiveMonitoring(true);
                } else {
                    alert('Gagal membuka kunci sesi.');
                }
            } catch (e) {
                alert('Terjadi kesalahan koneksi.');
            }
        }

        // Polling setiap 4 detik tanpa reload halaman
        setInterval(() => fetchLiveMonitoring(false), 4000);

        // Generate token via AJAX
        function regenerateToken(examId) {
            if (!confirm('Apakah Anda yakin ingin mengacak ulang token ujian? Siswa yang belum mulai akan membutuhkan token baru ini.')) return;
            
            fetch(`/monitoring/exams/${examId}/generate-token`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.token) {
                    document.getElementById('active-token').textContent = data.token;
                    const tokenEl = document.getElementById('active-token');
                    tokenEl.style.color = '#22c55e';
                    setTimeout(() => tokenEl.style.color = 'var(--text)', 1000);
                }
            })
            .catch(() => alert('Terjadi kesalahan saat mengacak token.'));
        }
    </script>
</x-layouts.app>
