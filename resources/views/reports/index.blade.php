<x-layouts.app :title="'Laporan Nilai - My Asssesmen'">
    <div style="margin-bottom:24px;">
        <h1 style="margin:0 0 4px; font-size:24px; color:var(--text);">Daftar Ujian & Laporan Nilai</h1>
        <p style="margin:0; color:var(--text-muted); font-size:14px;">Pilih salah satu ujian di bawah ini untuk melihat analisis dan mengekspor nilainya.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
        @forelse($exams as $exam)
            <a href="{{ route('reports.show', $exam) }}" style="text-decoration:none; display:block;">
                <div class="card" style="padding:16px; border:1px solid var(--border); border-radius:12px; transition:transform 0.2s, box-shadow 0.2s; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)'">
                    
                    <div style="display:inline-block; padding:4px 10px; border-radius:99px; background:#f0fdf4; color:#16a34a; font-size:11px; font-weight:700; margin-bottom:12px;">
                        {{ $exam->type ?? 'Ujian' }} / {{ $exam->semester ?? 'Ganjil' }}
                    </div>
                    
                    <h3 style="margin:0 0 4px; font-size:16px; color:var(--text); font-weight:700;">{{ $exam->title }}</h3>
                    <p style="margin:0 0 12px; font-size:13px; color:var(--text-muted);">
                        Kelas {{ $exam->classes->pluck('display_name')->join(', ') }}
                    </p>
                    
                    <div style="font-size:11px; color:#94a3b8; margin-top:8px;">
                        Tahun Pelajaran: {{ date('Y') }}/{{ date('Y')+1 }}
                    </div>
                </div>
            </a>
        @empty
            <div style="grid-column:1/-1; padding:48px; text-align:center; background:var(--surface); border-radius:12px; border:2px dashed var(--border);">
                <i class="fas fa-folder-open" style="font-size:48px; color:var(--border); margin-bottom:16px;"></i>
                <h3 style="margin:0 0 8px; color:var(--text-muted);">Belum Ada Ujian</h3>
                <p style="margin:0; color:#94a3b8; font-size:14px;">Ujian yang Anda buat akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
