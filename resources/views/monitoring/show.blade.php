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
            <div style="display:flex; align-items:center; gap:12px;">
                <span class="status-badge active" style="font-size:11px;">
                    <i class="fas fa-sync-alt fa-spin" style="margin-right:4px;"></i> Auto refresh 15s
                </span>
                <div style="background:#eff6ff; color:#1d4ed8; font-size:13px; font-weight:700; padding:6px 12px; border-radius:99px; border:1px solid #bfdbfe;">
                    Total Terdaftar: {{ $participants->count() }} Murid
                </div>
            </div>
        </div>
        <table style="border:none; margin:0;">
            <thead>
                <tr>
                    <th style="padding-left:24px; text-align:center; width:60px;">NO</th>
                    <th style="text-align:left;">NAMA MURID</th>
                    <th>NISN</th>
                    <th>STATUS</th>
                    <th style="padding-right:24px;">LOG TINDAKAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $index => $participant)
                    <tr style="border-bottom:1px solid var(--border);">
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
                                    Selesai
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
                                <form method="POST" action="{{ route('monitoring.unlock', $participant) }}" style="display:inline-block;">
                                    @csrf
                                    <button class="btn" type="submit" style="background:#f59e0b; color:#fff; font-weight:700; font-size:12px; padding:0 14px; min-height:30px; border:none;">
                                        Buka Kunci Sesi
                                    </button>
                                </form>
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

    <script>
        // Auto refresh halaman setiap 15 detik untuk live update
        setTimeout(() => location.reload(), 15000);

        // Generate token via AJAX
        function regenerateToken(examId) {
            if (!confirm('Apakah Anda yakin ingin mengacak ulang token ujian? Siswa yang belum mulai akan membutuhkan token baru ini.')) return;
            
            fetch(`/monitoring/exams/${examId}/generate-token`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.token) {
                    document.getElementById('active-token').textContent = data.token;
                    // Flash effect
                    const tokenEl = document.getElementById('active-token');
                    tokenEl.style.color = '#22c55e';
                    setTimeout(() => tokenEl.style.color = 'var(--text)', 1000);
                }
            })
            .catch(() => alert('Terjadi kesalahan saat mengacak token.'));
        }
    </script>
</x-layouts.app>
