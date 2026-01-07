@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>

    <div class="grid">
        <div class="glass stat-card fade-in" style="animation-delay: 0.1s;">
            <div class="stat-label">Total Buku</div>
            <div class="stat-value">{{ $totalBooks }}</div>
        </div>
        <div class="glass stat-card fade-in" style="animation-delay: 0.2s;">
            <div class="stat-label">Total Peminjaman</div>
            <div class="stat-value">{{ $totalLoans }}</div>
        </div>
        @if(isset($overdueCount) && $overdueCount > 0)
        <div class="glass stat-card fade-in" style="animation-delay: 0.25s; border-left: 3px solid var(--danger-color);">
            <div class="stat-label">Peminjaman Terlambat</div>
            <div class="stat-value" style="color: var(--danger-color);">{{ $overdueCount }}</div>
        </div>
        <div class="glass stat-card fade-in" style="animation-delay: 0.3s; border-left: 3px solid var(--danger-color);">
            <div class="stat-label">Total Denda Belum Dibayar</div>
            <div class="stat-value" style="color: var(--danger-color);">Rp {{ number_format($totalPendingFines ?? 0, 0, ',', '.') }}</div>
        </div>
        @endif
    </div>

    <!-- Charts Section -->
    <div class="grid" style="margin-top: 2rem; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
        <!-- Chart 1: Peminjaman per Bulan -->
        <div class="glass fade-in" style="animation-delay: 0.3s;">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">Peminjaman per Bulan</h3>
            <canvas id="monthlyLoansChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Chart 2: Status Peminjaman -->
        <div class="glass fade-in" style="animation-delay: 0.4s;">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">Status Peminjaman</h3>
            <canvas id="statusLoansChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Chart 3: Buku Terpopuler -->
        <div class="glass fade-in" style="animation-delay: 0.5s;">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">Buku Terpopuler</h3>
            <canvas id="popularBooksChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Chart 4: Anggota per Program Studi -->
        <div class="glass fade-in" style="animation-delay: 0.6s;">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">Anggota per Program Studi</h3>
            <canvas id="membersByProdiChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Chart 5: Peminjaman per Hari dalam Seminggu -->
        <div class="glass fade-in" style="animation-delay: 0.7s;">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">Peminjaman per Hari (30 Hari Terakhir)</h3>
            <canvas id="weeklyLoansChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Chart 6: Buku dengan Stok Terendah -->
        <div class="glass fade-in" style="animation-delay: 0.8s;">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">Buku dengan Stok Terendah</h3>
            <canvas id="lowStockBooksChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid" style="margin-top: 2rem;">
        <div class="glass fade-in" style="animation-delay: 0.8s;">
            <h3 style="margin-top: 0;">Buku Terpopuler</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Dipinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($popularBooks as $book)
                            <tr>
                                <td>{{ $book->title }}</td>
                                <td>{{ $book->loans_count }} kali</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: var(--text-muted);">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="glass fade-in" style="animation-delay: 0.9s;">
            <h3 style="margin-top: 0;">Peminjaman Terbaru</h3>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLoans as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->book->title }}</td>
                                <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-muted);">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Get theme colors
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#e2e8f0' : '#1e293b';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const primaryColor = isDark ? '#3b82f6' : '#0ea5e9';
        const accentColor = isDark ? '#8b5cf6' : '#6366f1';
        const successColor = isDark ? '#10b981' : '#059669';
        const dangerColor = isDark ? '#ef4444' : '#dc2626';
        const warningColor = isDark ? '#f59e0b' : '#d97706';

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: textColor
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                y: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                }
            }
        };

        // Chart 1: Peminjaman per Bulan (Line Chart)
        const monthlyCtx = document.getElementById('monthlyLoansChart');
        if (monthlyCtx) {
            const monthlyLabels = @json($monthlyLabels ?? []);
            const monthlyData = @json($monthlyData ?? []);
            
            if (monthlyLabels.length > 0) {
                new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [{
                            label: 'Jumlah Peminjaman',
                            data: monthlyData,
                            borderColor: primaryColor,
                            backgroundColor: primaryColor + '20',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: chartOptions
                });
            } else {
                monthlyCtx.parentElement.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data peminjaman</p>';
            }
        }

        // Chart 2: Status Peminjaman (Doughnut Chart)
        const statusCtx = document.getElementById('statusLoansChart');
        if (statusCtx) {
            const statusLabels = @json($statusLabels ?? []);
            const statusData = @json($statusData ?? []);
            
            if (statusLabels.length > 0) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusData,
                            backgroundColor: [
                                primaryColor,
                                successColor,
                                dangerColor,
                                warningColor
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: textColor
                                }
                            }
                        }
                    }
                });
            } else {
                statusCtx.parentElement.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data status</p>';
            }
        }

        // Chart 3: Buku Terpopuler (Bar Chart)
        const booksCtx = document.getElementById('popularBooksChart');
        if (booksCtx) {
            const bookLabels = @json($bookLabels ?? []);
            const bookData = @json($bookData ?? []);
            
            if (bookLabels.length > 0) {
                new Chart(booksCtx, {
                    type: 'bar',
                    data: {
                        labels: bookLabels,
                        datasets: [{
                            label: 'Jumlah Peminjaman',
                            data: bookData,
                            backgroundColor: accentColor + '80',
                            borderColor: accentColor,
                            borderWidth: 1
                        }]
                    },
                    options: chartOptions
                });
            } else {
                booksCtx.parentElement.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data buku</p>';
            }
        }

        // Chart 4: Anggota per Program Studi (Bar Chart)
        const prodiCtx = document.getElementById('membersByProdiChart');
        if (prodiCtx) {
            const prodiLabels = @json($prodiLabels ?? []);
            const prodiData = @json($prodiData ?? []);
            
            if (prodiLabels.length > 0) {
                new Chart(prodiCtx, {
                    type: 'bar',
                    data: {
                        labels: prodiLabels,
                        datasets: [{
                            label: 'Jumlah Anggota',
                            data: prodiData,
                            backgroundColor: successColor + '80',
                            borderColor: successColor,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        ...chartOptions,
                        indexAxis: 'y'
                    }
                });
            } else {
                prodiCtx.parentElement.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data anggota</p>';
            }
        }

        // Chart 5: Peminjaman per Hari dalam Seminggu (Bar Chart)
        const weeklyCtx = document.getElementById('weeklyLoansChart');
        if (weeklyCtx) {
            const dayLabels = @json($dayLabels ?? []);
            const weeklyData = @json($weeklyData ?? []);
            
            if (dayLabels.length > 0) {
                new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: dayLabels,
                        datasets: [{
                            label: 'Jumlah Peminjaman',
                            data: weeklyData,
                            backgroundColor: warningColor + '80',
                            borderColor: warningColor,
                            borderWidth: 1
                        }]
                    },
                    options: chartOptions
                });
            } else {
                weeklyCtx.parentElement.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data peminjaman</p>';
            }
        }

        // Chart 6: Buku dengan Stok Terendah (Bar Chart)
        const lowStockCtx = document.getElementById('lowStockBooksChart');
        if (lowStockCtx) {
            const lowStockLabels = @json($lowStockLabels ?? []);
            const lowStockData = @json($lowStockData ?? []);
            
            if (lowStockLabels.length > 0) {
                // Generate colors based on stock levels
                const backgroundColors = lowStockData.map(value => {
                    if (value <= 2) return dangerColor + '80';
                    if (value <= 5) return warningColor + '80';
                    return successColor + '80';
                });
                
                const borderColors = lowStockData.map(value => {
                    if (value <= 2) return dangerColor;
                    if (value <= 5) return warningColor;
                    return successColor;
                });

                new Chart(lowStockCtx, {
                    type: 'bar',
                    data: {
                        labels: lowStockLabels,
                        datasets: [{
                            label: 'Stok Tersedia',
                            data: lowStockData,
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        ...chartOptions,
                        indexAxis: 'y',
                        plugins: {
                            ...chartOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    afterLabel: function(context) {
                                        const value = context.parsed.x;
                                        if (value <= 2) {
                                            return '⚠️ Stok rendah! Perlu restock';
                                        } else if (value <= 5) {
                                            return '⚠️ Stok menipis';
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                lowStockCtx.parentElement.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data buku</p>';
            }
        }
    </script>
@endsection