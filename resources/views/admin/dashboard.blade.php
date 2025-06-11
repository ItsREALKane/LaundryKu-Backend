@extends('layouts.layout')

@section('title', 'Dashboard')

@section('header', 'Dashboard Admin')

@section('content')
<div class="grid lg:grid-cols-4 gap-6 mt-10">
    <!-- Total Pesanan Card -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <span class="iconify text-[#222831]" data-icon="solar:box-linear" style="font-size: 24px;"></span>
        </div>
        <h3 class="text-gray-500 text-sm">Total Pesanan</h3>
        <p class="text-2xl font-semibold mt-1">{{ $totalOrders }}</p>
        <p class="text-gray-500 text-sm mt-2">Bulan ini</p>
    </div>

    <!-- Menunggu Konfirmasi Card -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <span class="iconify text-[#F2994A]" data-icon="mdi:clock-outline" style="font-size: 24px;"></span>
        </div>
        <h3 class="text-gray-500 text-sm">Menunggu Konfirmasi</h3>
        <p class="text-2xl font-semibold mt-1">{{ $pendingOrders }}</p>
        <p class="text-gray-500 text-sm mt-2">Perlu diproses</p>
    </div>

    <!-- Dalam Proses Card -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <span class="iconify text-[#2D9CDB]" data-icon="mdi:progress-clock" style="font-size: 24px;"></span>
        </div>
        <h3 class="text-gray-500 text-sm">Dalam Proses</h3>
        <p class="text-2xl font-semibold mt-1">{{ $processingOrders }}</p>
        <p class="text-gray-500 text-sm mt-2">Sedang dikerjakan</p>
    </div>

    <!-- Selesai Card -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <span class="iconify text-[#27AE60]" data-icon="mdi:check-circle-outline" style="font-size: 24px;"></span>
        </div>
        <h3 class="text-gray-500 text-sm">Selesai</h3>
        <p class="text-2xl font-semibold mt-1">{{ $completedOrders }}</p>
        <p class="text-gray-500 text-sm mt-2">Siap diambil</p>
    </div>
</div>

<!-- Transaksi Terbaru -->
<div class="mt-6 bg-white p-6 shadow rounded-lg">
    <h2 class="text-lg font-semibold mb-4">Transaksi Terbaru</h2>
    
    @forelse($latestTransactions as $transaction)
    <div class="mt-4 p-4 bg-gray-100 rounded-lg flex justify-between items-center">
        <div>
            <p class="font-semibold">{{ $transaction->customer->name }}</p>
            <p class="text-gray-500 text-sm">{{ $transaction->customer->phone }}</p>
        </div>
        <p class="text-gray-400 text-sm max-w-md">
            {{ $transaction->customer->address }}
        </p>
        <div class="text-right">
            <p class="text-gray-600">{{ $transaction->created_at->format('d-m-Y') }}</p>
            <p class="text-gray-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
        </div>
    </div>
    @empty
    <div class="text-center text-gray-500 py-4">
        Belum ada transaksi
    </div>
    @endforelse

    @if($latestTransactions->isNotEmpty())
    <div class="mt-4 text-center">
        <a href="{{ route('admin.orders') }}" class="text-[#00ADB5] hover:text-[#008C94]">
            Lihat Semua Transaksi
        </a>
    </div>
    @endif
</div>

<!-- Monthly Stats Chart -->
<div class="mt-6 bg-white p-6 shadow rounded-lg">
    <h2 class="text-lg font-semibold mb-4">Statistik Bulanan</h2>
    <canvas id="monthlyStatsChart"></canvas>
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Stats Chart
    const ctx = document.getElementById('monthlyStatsChart').getContext('2d');
    const monthlyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: JSON.parse('{!! addslashes(json_encode($monthlyStats->pluck("month"))) !!}'),
            datasets: [{
                label: 'Total Pesanan',
                data: JSON.parse('{!! addslashes(json_encode($monthlyStats->pluck("total_orders"))) !!}'),
                borderColor: '#00ADB5',
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Jumlah Pesanan per Bulan'
                }
            },
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
</script>
@endpush

@endsection