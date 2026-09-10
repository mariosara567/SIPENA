<x-layouts.student :title="'Ujian Selesai - My Asssesmen'" :fullscreen="true">
    <style>
        .success-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 90vh;
            padding: 20px;
        }
        
        .success-card {
            background: #fff;
            width: 100%;
            max-width: 480px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
        }

        .success-header {
            background: #0284c7;
            padding: 30px 20px;
            text-align: center;
        }

        .check-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            background: #22c55e;
            color: #fff;
            border-radius: 50%;
            font-size: 32px;
            box-shadow: 0 0 0 8px rgba(34, 197, 94, 0.2);
        }

        .success-body {
            padding: 24px;
        }

        .info-alert {
            background: #eff6ff;
            border-radius: 8px;
            padding: 14px;
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .info-alert i { color: #2563eb; font-size: 18px; margin-top: 2px; }
        .info-alert p { margin: 0; font-size: 12px; color: #1e293b; line-height: 1.5; }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #64748b; }
        .detail-value { font-weight: 700; color: #0f172a; text-align: right; max-width: 60%; line-height: 1.4; }

        .success-footer {
            padding: 16px 24px 24px;
        }
        .btn-exit {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background: #e11d48;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            gap: 8px;
            transition: opacity 0.2s;
        }
        .btn-exit:hover { opacity: 0.9; }
    </style>

    <div class="success-wrapper">
        <div class="success-card">
        <div class="success-header">
            <div class="check-icon">
                <i class="fas fa-check"></i>
            </div>
        </div>

        <div class="success-body">
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <p>Nilai Anda telah disimpan di database sekolah. Hasil akhir akan diperiksa dan diumumkan langsung oleh guru mata pelajaran.</p>
            </div>

            <div class="detail-row">
                <span class="detail-label">Mata Ujian</span>
                <span class="detail-value">{{ $exam->subject->name }} ({{ $exam->title }})</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kelas</span>
                <span class="detail-value">{{ auth()->user()->student->schoolClass?->display_name ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Jumlah Soal</span>
                <span class="detail-value">{{ $answeredCount }} Butir Terjawab</span>
            </div>
        </div>

        <div class="success-footer">
            <a href="{{ route('student.exams.index') }}" class="btn-exit">
                <i class="fas fa-sign-out-alt"></i> Keluar & Kembali ke Beranda
            </a>
        </div>
    </div>
    </div>
</x-layouts.student>
