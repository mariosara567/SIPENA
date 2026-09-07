<x-layouts.app :title="$module['title'] ?? 'Dashboard'">
    <div class="header-section">
        <h1>{{ $module['title'] }}</h1>
        <p>{{ $module['subtitle'] }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        @foreach(array_slice($module['items'], 0, 4) as $index => $item)
            <div class="stat-card {{ ['primary','success','warning','primary'][$index] ?? 'primary' }}">
                <div class="stat-icon"><i class="{{ $item['icon'] ?? 'fas fa-chart-simple' }}"></i></div>
                <div class="stat-content">
                    <div class="stat-value"><span class="stat-number">{{ $item['count'] ?? 0 }}</span></div>
                    <p class="stat-label">{{ $item['name'] ?? 'Item' }}</p>
                    <p class="stat-description">{{ $item['description'] ?? 'Total data' }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Module Cards -->
    <h2 class="section-title">Modul Tersedia</h2>
    <div class="modules-grid">
        @forelse ($module['items'] as $item)
            <a href="{{ $item['url'] ?? route($item['route'] ?? '#') }}" class="module-card">
                <div class="module-icon">
                    <i class="{{ $item['icon'] ?? 'fas fa-folder' }}"></i>
                </div>
                <h3 class="module-title">{{ $item['name'] }}</h3>
                <p class="module-description">{{ $item['description'] ?? 'Kelola ' . strtolower($item['name']) }}</p>
                <div class="module-footer">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 40px; color: var(--text-muted);">
                <i style="font-size: 56px; margin-bottom: 20px; opacity: 0.4;" class="fas fa-inbox"></i>
                <p style="font-size: 16px; font-weight: 600;">Tidak ada modul yang tersedia</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
