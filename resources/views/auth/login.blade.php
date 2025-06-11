@extends('layouts.layout')

@section('title', 'Login')

@section('content')
<div class="h-screen w-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md px-6">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 h-24 object-contain">
            </div>

            <h1 class="text-3xl font-bold text-[#00ADB5] mb-6 text-center">Admin Login</h1>

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="relative mt-4">
                    <span class="iconify absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg" data-icon="mdi:account"></span>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Masukkan Name Admin"
                        class="w-full pl-10 pr-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5] @error('name') border-red-500 @enderror"
                    >
                </div>
                @error('name')
                    <p class="mt-1 text-red-500 text-sm">{{ $message }}</p>
                @enderror

                <div class="relative mt-4">
                    <span class="iconify absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg" data-icon="mdi:lock"></span>
                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan Password"
                        class="w-full pl-10 pr-10 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#00ADB5] @error('password') border-red-500 @enderror"
                    >
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 toggle-password"
                        onclick="togglePassword(this)"
                    >
                        <span class="iconify" data-icon="mdi:eye-off-outline"></span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-red-500 text-sm">{{ $message }}</p>
                @enderror

                @if(session('error'))
                    <div class="mt-4 text-red-500 text-sm text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <button 
                    type="submit"
                    class="w-full mt-6 py-3 bg-[#00ADB5] text-white rounded-full transition hover:bg-[#008C94]"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(button) {
    const input = button.parentElement.querySelector('input');
    const icon = button.querySelector('.iconify');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-icon', 'mdi:eye-outline');
    } else {
        input.type = 'password';
        icon.setAttribute('data-icon', 'mdi:eye-off-outline');
    }
}
</script>
@endpush
@endsection