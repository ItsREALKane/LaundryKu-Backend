@extends('layouts.layout')

@section('title', 'Pesanan')

@section('header', 'Daftar Pesanan')

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

        <div class="relative" x-data="{ showFilter: false }">
            <button 
                type="button" 
                class="p-2 rounded bg-gray-100 hover:bg-gray-300 transition-colors flex items-center gap-2 text-black shadow"
                @click="showFilter = !showFilter"
            >
                <span class="iconify" data-icon="mdi:filter-variant"></span>
            </button>

            <div 
                x-show="showFilter"
                @click.away="showFilter = false"
                class="absolute top-full right-0 mt-2 bg-white border rounded shadow-lg z-10 p-3"
                style="display: none;"
            >
                <div class="mb-2 font-semibold">Filter Status</div>
                <select
                    id="statusFilter"
                    class="border rounded px-2 py-1 w-40"
                >
                    <option value="">Semua Status</option>
                    <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                    <option value="diproses">Diproses</option>
                    <option value="selesai">Selesai</option>
                    <option value="dikembalikan">Dikembalikan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-gray-100 p-4 shadow rounded-lg overflow-x-auto">
        <div class="min-w-[1000px]">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm">
                        <th class="py-2 px-4 text-center">No</th>
                        <th class="py-2 px-4 text-center">Nama</th>
                        <th class="py-2 px-4 text-center">No. HP</th>
                        <th class="py-2 px-4 text-center">Alamat</th>
                        <th class="py-2 px-4 text-center">Tanggal Pesan</th>
                        <th class="py-2 px-4 text-center">Catatan</th>
                        <th class="py-2 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="bg-white text-sm text-gray-600">
                        <td class="py-3 px-4 text-center">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4 text-center">{{ $order->user->name }}</td>
                        <td class="py-3 px-4 text-center">{{ $order->user->phone }}</td>
                        <td class="py-3 px-4 text-center">{{ $order->alamat }}</td>
                        <td class="py-3 px-4 text-center">{{ $order->tanggal_pesanan->format('d-m-Y') }}</td>
                        <td class="py-3 px-4 text-center">{{ $order->catatan }}</td>
                        <td class="py-3 px-4 text-center">
                            <select
                                class="border rounded px-2 py-1"
                                data-order-id="{{ $order->id }}"
                                onchange="updateStatus(this)"
                            >
                                <option value="menunggu_konfirmasi" {{ $order->status == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dikembalikan" {{ $order->status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-gray-500">Belum ada pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    // Search functionality
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('statusFilter');
    let debounceTimer;

    function filterOrders() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchQuery = searchInput.value;
            const statusQuery = statusFilter.value;
            
            window.location.href = `{{ route('admin.orders') }}?search=${searchQuery}&status=${statusQuery}`;
        }, 500);
    }

    searchInput.addEventListener('input', filterOrders);
    statusFilter.addEventListener('change', filterOrders);

    // Update order status
    function updateStatus(selectElement) {
        const orderId = selectElement.dataset.orderId;
        const newStatus = selectElement.value;

        fetch(`{{ route('admin.orders.update-status') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: orderId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (newStatus === 'selesai') {
                    window.location.href = '{{ route("admin.bills") }}';
                }
            } else {
                alert('Gagal mengupdate status pesanan');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupdate status');
            location.reload();
        });
    }
</script>
@endpush

@endsection