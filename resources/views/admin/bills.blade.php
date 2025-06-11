@extends('layouts.layout')

@section('title', 'Tagihan')

@section('header', 'Daftar Tagihan')

@section('content')
<div class="flex flex-col space-y-6">
    <!-- Search Bar -->
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
    </div>

    <!-- Bills Table -->
    <div class="bg-gray-100 p-4 shadow rounded-lg overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm">
                    <th class="py-2 px-4 text-center">No</th>
                    <th class="py-2 px-4 text-center">Nama</th>
                    <th class="py-2 px-4 text-center">No. HP</th>
                    <th class="py-2 px-4 text-center">Alamat</th>
                    <th class="py-2 px-4 text-center">Tagihan</th>
                    <th class="py-2 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr class="bg-white text-sm text-gray-600">
                    <td class="py-3 px-4 text-center">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 text-center">{{ $bill->user->name }}</td>
                    <td class="py-3 px-4 text-center">{{ $bill->user->phone }}</td>
                    <td class="py-3 px-4 text-center">{{ $bill->alamat }}</td>
                    <td class="py-3 px-4 text-center">Rp {{ number_format($bill->total_harga, 0, ',', '.') }}</td>
                    <td class="py-3 px-4 text-center">
                        <button
                            type="button"
                            class="p-2 bg-[#00ADB5] hover:bg-[#129990] text-white rounded flex items-center justify-center mx-auto price-dialog-btn"
                            data-bill-id="{{ $bill->id }}"
                            data-current-price="{{ $bill->total_harga ?? 0 }}"
                        >
                            <span class="iconify mr-1" data-icon="mdi:cash-plus"></span>
                            {{ $bill->total_harga ? 'Edit Harga' : 'Tentukan Harga' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-gray-500">Belum ada tagihan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $bills->links() }}
        </div>
    </div>
</div>

<!-- Price Dialog -->
<div id="priceDialog" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 min-w-[320px]">
        <h2 class="text-lg font-semibold mb-4" id="dialogTitle">Tentukan Harga Tagihan</h2>
        <input
            type="number"
            id="priceInput"
            class="border rounded px-3 py-2 w-full mb-4"
            placeholder="Masukkan harga (Rp)"
            min="0"
        >
        <div class="flex justify-end gap-2">
            <button 
                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400"
                onclick="closePriceDialog()"
            >Batal</button>
            <button 
                id="saveButton"
                class="px-4 py-2 rounded bg-blue-500 hover:bg-blue-600 text-white"
                onclick="savePrice()"
            >Simpan</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    const searchInput = document.getElementById('search');
    let debounceTimer;

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchQuery = searchInput.value;
            window.location.href = `{{ route('admin.bills') }}?search=${searchQuery}`;
        }, 500);
    });

    // Price dialog functionality
    let currentBillId = null;

    // Add event listeners to all price dialog buttons
    document.querySelectorAll('.price-dialog-btn').forEach(button => {
        button.addEventListener('click', function() {
            const billId = this.dataset.billId;
            const currentPrice = this.dataset.currentPrice;
            openPriceDialog(billId, currentPrice);
        });
    });

    function openPriceDialog(billId, currentPrice) {
        currentBillId = billId;
        const dialog = document.getElementById('priceDialog');
        const input = document.getElementById('priceInput');
        const title = document.getElementById('dialogTitle');

        title.textContent = currentPrice > 0 ? 'Edit Harga Tagihan' : 'Tentukan Harga Tagihan';
        input.value = currentPrice;
        dialog.classList.remove('hidden');
    }

    function closePriceDialog() {
        const dialog = document.getElementById('priceDialog');
        dialog.classList.add('hidden');
        currentBillId = null;
    }

    function savePrice() {
        const input = document.getElementById('priceInput');
        const price = parseInt(input.value);

        if (isNaN(price) || price <= 0) {
            alert('Masukkan harga yang valid!');
            return;
        }

        const saveButton = document.getElementById('saveButton');
        saveButton.disabled = true;
        saveButton.textContent = 'Menyimpan...';

        fetch(`{{ route('admin.bills.update-price') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                bill_id: currentBillId,
                price: price
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menyimpan harga!');
                saveButton.disabled = false;
                saveButton.textContent = 'Simpan';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan harga!');
            saveButton.disabled = false;
            saveButton.textContent = 'Simpan';
        });
    }
</script>
@endpush

@endsection