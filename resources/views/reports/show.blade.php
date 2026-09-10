<x-layouts.app :title="'Laporan Nilai & Analisis Ujian'">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h1 style="margin:0; font-size:24px; color:var(--text);">Daftar Nilai & Analisis Ujian</h1>
    </div>

    {{-- Horizontal Scrollable Exams Slider --}}
    <div style="display:flex; gap:16px; overflow-x:auto; padding-bottom:16px; margin-bottom:16px; scroll-behavior:smooth;">
        @foreach($allExams as $item)
            @php($isSelected = $item->id === $exam->id)
            <a href="{{ route('reports.show', $item) }}" style="text-decoration:none; flex:0 0 280px;">
                <div class="card" style="padding:16px; border:1px solid {{ $isSelected ? 'var(--primary)' : 'var(--border)' }}; border-radius:12px; position:relative; background:{{ $isSelected ? '#f8fafc' : '#fff' }}; height:100%;">
                    @if($isSelected)
                        <div style="position:absolute; top:0; right:0; background:var(--primary); color:#fff; font-size:10px; font-weight:700; padding:4px 10px; border-bottom-left-radius:12px; border-top-right-radius:11px;">
                            Terpilih
                        </div>
                    @endif
                    
                    <div style="display:inline-block; padding:4px 0; color:{{ $isSelected ? '#d97706' : '#16a34a' }}; font-size:11px; font-weight:700; margin-bottom:6px;">
                        {{ $item->type ?? 'Ujian' }} / {{ $item->semester ?? 'Ganjil' }}
                    </div>
                    
                    <h3 style="margin:0 0 4px; font-size:15px; color:var(--text); font-weight:700;">{{ $item->title }}</h3>
                    <p style="margin:0 0 10px; font-size:12px; color:var(--text-muted);">
                        Kelas {{ $item->classes->pluck('display_name')->join(', ') }}
                    </p>
                    
                    <div style="font-size:10px; color:#94a3b8;">
                        Tahun Pelajaran: {{ date('Y') }}/{{ date('Y')+1 }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Grafik Analisis Nilai --}}
    <section class="card" style="margin-bottom:24px; padding:24px;">
        <h2 style="margin:0 0 20px; font-size:18px; color:var(--text);">Grafik Analisis Nilai</h2>
        
        <div style="display:flex; flex-direction:column; gap:20px;">
            {{-- Sangat Baik --}}
            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:700; color:var(--text);">Rentang 93 - 100 (Sangat Baik)</span>
                    <span style="font-size:13px; font-weight:700; color:#2563eb;">{{ $stats['sangat_baik']['count'] }} Murid ({{ $stats['sangat_baik']['percent'] }}%)</span>
                </div>
                <div style="width:100%; height:16px; background:#f1f5f9; border-radius:99px; overflow:hidden;">
                    <div style="width:{{ $stats['sangat_baik']['percent'] }}%; height:100%; background:#2563eb; border-radius:99px;"></div>
                </div>
            </div>
            
            {{-- Baik --}}
            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:700; color:var(--text);">Rentang 85 - 92 (Baik)</span>
                    <span style="font-size:13px; font-weight:700; color:#22c55e;">{{ $stats['baik']['count'] }} Murid ({{ $stats['baik']['percent'] }}%)</span>
                </div>
                <div style="width:100%; height:16px; background:#f1f5f9; border-radius:99px; overflow:hidden;">
                    <div style="width:{{ $stats['baik']['percent'] }}%; height:100%; background:#22c55e; border-radius:99px;"></div>
                </div>
            </div>

            {{-- Cukup --}}
            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:700; color:var(--text);">Rentang 76 - 84 (Cukup / Batas Lulus)</span>
                    <span style="font-size:13px; font-weight:700; color:#14b8a6;">{{ $stats['cukup']['count'] }} Murid ({{ $stats['cukup']['percent'] }}%)</span>
                </div>
                <div style="width:100%; height:16px; background:#f1f5f9; border-radius:99px; overflow:hidden;">
                    <div style="width:{{ $stats['cukup']['percent'] }}%; height:100%; background:#14b8a6; border-radius:99px;"></div>
                </div>
            </div>

            {{-- Perlu Bimbingan --}}
            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:700; color:var(--text);">Rentang 0 - 75 (Perlu Bimbingan / Di Bawah KKM)</span>
                    <span style="font-size:13px; font-weight:700; color:#ef4444;">{{ $stats['kurang']['count'] }} Murid ({{ $stats['kurang']['percent'] }}%)</span>
                </div>
                <div style="width:100%; height:16px; background:#f1f5f9; border-radius:99px; overflow:hidden;">
                    <div style="width:{{ $stats['kurang']['percent'] }}%; height:100%; background:#ef4444; border-radius:99px;"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Daftar Nilai --}}
    <section class="card table-wrap" style="padding:0; overflow:hidden;">
        <table style="margin:0; border:none;">
            <thead>
                <tr style="background:#f0f9ff; border-bottom:1px solid #bae6fd;">
                    <th style="padding-left:24px; width:60px;">No</th>
                    <th style="text-align:left;">Nama Murid</th>
                    <th>NISN</th>
                    <th>Nilai Ujian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $index => $participant)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding-left:24px; text-align:center; color:#94a3b8;">{{ $index + 1 }}</td>
                        <td style="text-align:left; font-weight:600; color:var(--text);">{{ $participant->student->user->name }}</td>
                        <td style="color:#64748b; text-align:center;">{{ $participant->student->nisn }}</td>
                        <td style="text-align:center;">
                            <span style="font-weight:700; color:{{ $participant->score >= 76 ? 'var(--text)' : '#ef4444' }};">
                                {{ $participant->score }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada nilai yang masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{-- Tombol Ekspor --}}
    <div style="text-align:right; margin-top:24px;">
        <a href="{{ route('reports.export', $exam) }}" class="btn" style="background:#10b981; color:#fff; border:none; padding:12px 24px; font-weight:700; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-file-excel"></i> Ekspor Hasil Nilai ke Excel (.xlsx)
        </a>
    </div>

    <style>
        /* Custom scrollbar for horizontal slider */
        div[style*="overflow-x:auto"]::-webkit-scrollbar {
            height: 6px;
        }
        div[style*="overflow-x:auto"]::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        div[style*="overflow-x:auto"]::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        div[style*="overflow-x:auto"]::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</x-layouts.app>
