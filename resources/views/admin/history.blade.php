@extends('layouts.layout')

@section('title', 'Riwayat')

@section('header', 'Riwayat Pesanan')

@section('content')
<div class="flex flex-col space-y-6">
    <!-- Search and Filter -->
    <div class="flex items-center gap-3">
        <div class="flex-1">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <span class="iconify text-gray-500" data-icon="mdi:magnify"></span>
                </span>
                <input
                    type="text"
                    name="search"
                    id="search"
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                    placeholder="Cari berdasarkan nama, nomor HP, atau alamat..."
                >
            </div>
        </div>

        <!-- Date Filter -->
        <div class="flex items-center gap-2">
            <input
                type="date"
                id="startDate"
                class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
            >
            <span class="text-gray-500">-</span>
            <input
                type="date"
                id="endDate"
                class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
            >
        </div>

        <!-- Export Button -->
        <button
            onclick="exportHistory()"
            class="px-4 py-2 bg-[#00ADB5] text-white rounded hover:bg-[#008C94] flex items-center gap-2"
        >
            <span class="iconify" data-icon="mdi:file-export"></span>
            Export
        </button>
    </div>

    <!-- History Table -->
    <div class="bg-gray-100 p-4 shadow rounded-lg overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm">
                    <th class="py-2 px-4 text-center">No</th>
                    <th class="py-2 px-4 text-center">Nama</th>
                    <th class="py-2 px-4 text-center">No. HP</th>
                    <th class="py-2 px-4 text-center">Alamat</th>
                    <th class="py-2 px-4 text-center">Tanggal</th>
                    <th class="py-2 px-4 text-center">Status</th>
                    <th class="py-2 px-4 text-center">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $order)
                <tr class="bg-white text-sm text-gray-600">
                    <td class="py-3 px-4 text-center">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 text-center">{{ $order->user->name }}</td>
                    <td class="py-3 px-4 text-center">{{ $order->user->phone }}</td>
                    <td class="py-3 px-4 text-center">{{ $order->alamat }}</td>
                    <td class="py-3 px-4 text-center">{{ $order->tanggal_pesanan->format('d-m-Y') }}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2 py-1 rounded text-sm
                            @if($order->status == 'selesai') bg-green-100 text-green-800
                            @elseif($order->status == 'dibatalkan') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif"
                        >
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-4 text-center text-gray-500">Belum ada riwayat pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search and filter functionality
    const searchInput = document.getElementById('search');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    let debounceTimer;

    function filterHistory() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchQuery = searchInput.value;
            const startDateValue = startDate.value;
            const endDateValue = endDate.value;
            
            window.location.href = `{{ route('admin.history') }}?search=${searchQuery}&start_date=${startDateValue}&end_date=${endDateValue}`;
        }, 500);
    }

    searchInput.addEventListener('input', filterHistory);
    startDate.addEventListener('change', filterHistory);
    endDate.addEventListener('change', filterHistory);

    // Export functionality
    function exportHistory() {
        const searchQuery = searchInput.value;
        const startDateValue = startDate.value;
        const endDateValue = endDate.value;

        window.location.href = `{{ route('admin.history.export') }}?search=${searchQuery}&start_date=${startDateValue}&end_date=${endDateValue}`;
    }

    // Set initial date values if they exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('start_date')) startDate.value = urlParams.get('start_date');
    if (urlParams.has('end_date')) endDate.value = urlParams.get('end_date');
    if (urlParams.has('search')) searchInput.value = urlParams.get('search');
</script>
@endpush

@endsection