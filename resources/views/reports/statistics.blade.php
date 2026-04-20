@extends('layouts.app')

@section('title', 'Statistik Perpustakaan')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i>
                Filter Statistik
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="year" class="form-label">Pilih Tahun</label>
                        <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-clock"></i>
                Rata-rata Durasi Peminjaman
            </div>
            <div class="card-body text-center">
                <h2 class="text-primary">{{ $avgDuration->avg_duration ? round($avgDuration->avg_duration, 1) : 0 }} Hari</h2>
                <p class="text-muted">Rata-rata waktu peminjaman buku</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-money-bill-wave"></i>
                Total Denda Terkumpul
            </div>
            <div class="card-body text-center">
                <h2 class="text-danger">Rp {{ number_format($totalFine, 0, ',', '.') }}</h2>
                <p class="text-muted">Total denda dari peminjaman tahun {{ $year }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-book"></i>
                Top 5 Buku Paling Sering Dipinjam
            </div>
            <div class="card-body">
                <canvas id="topBooksChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-users"></i>
                Top 5 Anggota Paling Aktif
            </div>
            <div class="card-body">
                <canvas id="topMembersChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="glass-card">
            <div class="card-header">
                <i class="fas fa-calendar-alt"></i>
                Grafik Peminjaman Per Bulan ({{ $year }})
            </div>
            <div class="card-body">
                <canvas id="monthlyLoansChart" width="800" height="400"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Top 5 Books Chart
    const topBooksCtx = document.getElementById('topBooksChart').getContext('2d');
    const topBooksData = @json($topBooks);
    new Chart(topBooksCtx, {
        type: 'bar',
        data: {
            labels: topBooksData.map(item => item.title.length > 20 ? item.title.substring(0, 20) + '...' : item.title),
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: topBooksData.map(item => item.loan_count),
                backgroundColor: 'rgba(13, 148, 136, 0.6)',
                borderColor: 'rgba(13, 148, 136, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Top 5 Members Chart
    const topMembersCtx = document.getElementById('topMembersChart').getContext('2d');
    const topMembersData = @json($topMembers);
    new Chart(topMembersCtx, {
        type: 'bar',
        data: {
            labels: topMembersData.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name),
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: topMembersData.map(item => item.loan_count),
                backgroundColor: 'rgba(212, 175, 55, 0.6)',
                borderColor: 'rgba(212, 175, 55, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Monthly Loans Chart
    const monthlyCtx = document.getElementById('monthlyLoansChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthNames,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: Object.values(monthlyData),
                backgroundColor: 'rgba(30, 58, 95, 0.1)',
                borderColor: 'rgba(30, 58, 95, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endsection