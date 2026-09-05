<x-layouts.app :title="'Sinkronisasi - SIPENA'">
    <section class="grid two">
        <article class="card"><h3 style="margin:0;">Pending Sync</h3><p style="font-size:28px;margin:8px 0 0;">{{ $pendingCount }}</p></article>
        <article class="card"><h3 style="margin:0;">Sudah Synced</h3><p style="font-size:28px;margin:8px 0 0;">{{ $syncedCount }}</p></article>
    </section>

    <section class="card">
        <form method="POST" action="{{ route('sync.run') }}">
            @csrf
            <button class="btn btn-primary" type="submit">Jalankan Sinkronisasi Sekarang</button>
        </form>
    </section>

    <section class="card table-wrap">
        <h2 style="margin-top:0;">Riwayat Sinkronisasi</h2>
        <table>
            <thead><tr><th>Waktu</th><th>Tipe</th><th>Total</th><th>Sukses</th><th>Gagal</th><th>Status</th><th>Pesan</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->sync_date->format('d M Y H:i:s') }}</td>
                    <td>{{ $log->sync_type }}</td>
                    <td>{{ $log->total_records }}</td>
                    <td>{{ $log->success_records }}</td>
                    <td>{{ $log->failed_records }}</td>
                    <td>{{ $log->status }}</td>
                    <td>{{ $log->message }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada log sinkronisasi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.app>
