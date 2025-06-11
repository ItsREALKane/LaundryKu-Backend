@extends('layouts.layout')

@section('title', 'Pengaturan')

@section('header', 'Pengaturan Laundry')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-4xl">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Tabs -->
            <div class="flex border-b" x-data="{ activeTab: 'general' }">
                <button 
                    @click="activeTab = 'general'"
                    :class="{'bg-[#00ADB5] text-white': activeTab === 'general', 'hover:bg-gray-100': activeTab !== 'general'}"
                    class="flex-1 py-4 px-6 text-center font-medium transition duration-150 ease-in-out"
                >
                    <span class="iconify inline-block mr-2" data-icon="mdi:store-outline"></span>
                    Informasi Umum
                </button>
                <button 
                    @click="activeTab = 'services'"
                    :class="{'bg-[#00ADB5] text-white': activeTab === 'services', 'hover:bg-gray-100': activeTab !== 'services'}"
                    class="flex-1 py-4 px-6 text-center font-medium transition duration-150 ease-in-out"
                >
                    <span class="iconify inline-block mr-2" data-icon="mdi:washing-machine"></span>
                    Layanan
                </button>
                <button 
                    @click="activeTab = 'hours'"
                    :class="{'bg-[#00ADB5] text-white': activeTab === 'hours', 'hover:bg-gray-100': activeTab !== 'hours'}"
                    class="flex-1 py-4 px-6 text-center font-medium transition duration-150 ease-in-out"
                >
                    <span class="iconify inline-block mr-2" data-icon="mdi:clock-outline"></span>
                    Jam Operasional
                </button>
            </div>

            <!-- Tab Contents -->
            <div class="p-6" x-data="{ 
                imagePreview: '{{ $laundry->logo_url ?? "" }}',
                services: @json($services ?? []),
                newService: { name: '', price: '', description: '' },
                operationalHours: @json($operationalHours ?? []),
                addService() {
                    this.services.push({...this.newService});
                    this.newService = { name: '', price: '', description: '' };
                },
                removeService(index) {
                    this.services.splice(index, 1);
                }
            }">
                <!-- General Settings Tab -->
                <div x-show="activeTab === 'general'">
                    <form action="{{ route('admin.settings.update-general') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Logo Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logo Laundry</label>
                                <div class="flex items-center space-x-4">
                                    <div class="w-24 h-24 border-2 border-gray-300 rounded-lg overflow-hidden">
                                        <template x-if="imagePreview">
                                            <img :src="imagePreview" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!imagePreview">
                                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                <span class="iconify text-gray-400" data-icon="mdi:image-outline" style="font-size: 2rem;"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <div>
                                        <input 
                                            type="file" 
                                            name="logo" 
                                            id="logo"
                                            class="hidden"
                                            accept="image/*"
                                            @change="imagePreview = URL.createObjectURL($event.target.files[0])"
                                        >
                                        <button 
                                            type="button"
                                            onclick="document.getElementById('logo').click()"
                                            class="px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200 transition"
                                        >
                                            <span class="iconify inline-block mr-1" data-icon="mdi:upload"></span>
                                            Upload Logo
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Laundry</label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        value="{{ old('name', $laundry->name ?? '') }}"
                                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Laundry</label>
                                    <input 
                                        type="text" 
                                        name="type" 
                                        value="{{ old('type', $laundry->type ?? '') }}"
                                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                    <textarea 
                                        name="address" 
                                        rows="3"
                                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                    >{{ old('address', $laundry->address ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                                    <input 
                                        type="text" 
                                        name="phone" 
                                        value="{{ old('phone', $laundry->phone ?? '') }}"
                                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                    >
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-[#00ADB5] text-white rounded-md hover:bg-[#008C94] transition">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Services Tab -->
                <div x-show="activeTab === 'services'">
                    <form action="{{ route('admin.settings.update-services') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Services List -->
                            <template x-for="(service, index) in services" :key="index">
                                <div class="bg-gray-50 p-4 rounded-md">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <input 
                                            type="text" 
                                            x-model="service.name"
                                            :name="`services[${index}][name]`"
                                            placeholder="Nama Layanan"
                                            class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                        >
                                        <input 
                                            type="number" 
                                            x-model="service.price"
                                            :name="`services[${index}][price]`"
                                            placeholder="Harga"
                                            class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                        >
                                        <div class="flex items-center space-x-2">
                                            <input 
                                                type="text" 
                                                x-model="service.description"
                                                :name="`services[${index}][description]`"
                                                placeholder="Deskripsi"
                                                class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                            >
                                            <button 
                                                type="button" 
                                                @click="removeService(index)"
                                                class="p-2 text-red-500 hover:bg-red-100 rounded-md transition"
                                            >
                                                <span class="iconify" data-icon="mdi:trash-can-outline"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Add New Service -->
                            <div class="bg-gray-50 p-4 rounded-md">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <input 
                                        type="text" 
                                        x-model="newService.name"
                                        placeholder="Nama Layanan"
                                        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                    >
                                    <input 
                                        type="number" 
                                        x-model="newService.price"
                                        placeholder="Harga"
                                        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                    >
                                    <div class="flex items-center space-x-2">
                                        <input 
                                            type="text" 
                                            x-model="newService.description"
                                            placeholder="Deskripsi"
                                            class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                        >
                                        <button 
                                            type="button" 
                                            @click="addService()"
                                            class="p-2 text-green-500 hover:bg-green-100 rounded-md transition"
                                        >
                                            <span class="iconify" data-icon="mdi:plus"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-[#00ADB5] text-white rounded-md hover:bg-[#008C94] transition">
                                    Simpan Layanan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Operational Hours Tab -->
                <div x-show="activeTab === 'hours'">
                    <form action="{{ route('admin.settings.update-hours') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 gap-4">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-md">
                                    <div class="w-24">
                                        <span class="font-medium">{{ $day }}</span>
                                    </div>
                                    <div class="flex-1 grid grid-cols-2 gap-4">
                                        <input 
                                            type="time" 
                                            name="hours[{{ strtolower($day) }}][open]" 
                                            value="{{ old('hours.'.strtolower($day).'.open', $operationalHours[strtolower($day)]['open'] ?? '') }}"
                                            class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                        >
                                        <input 
                                            type="time" 
                                            name="hours[{{ strtolower($day) }}][close]" 
                                            value="{{ old('hours.'.strtolower($day).'.close', $operationalHours[strtolower($day)]['close'] ?? '') }}"
                                            class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5]"
                                        >
                                    </div>
                                    <div class="w-24 text-right">
                                        <label class="inline-flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="hours[{{ strtolower($day) }}][closed]" 
                                                value="1"
                                                {{ old('hours.'.strtolower($day).'.closed', $operationalHours[strtolower($day)]['closed'] ?? false) ? 'checked' : '' }}
                                                class="form-checkbox h-5 w-5 text-[#00ADB5]"
                                            >
                                            <span class="ml-2 text-sm">Tutup</span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-[#00ADB5] text-white rounded-md hover:bg-[#008C94] transition">
                                    Simpan Jam Operasional
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@endsection