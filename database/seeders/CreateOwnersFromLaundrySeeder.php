<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Laundry;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateOwnersFromLaundrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat data owner berdasarkan data laundry yang ada
     */
    public function run(): void
    {
        // Ambil semua laundry yang belum memiliki owner
        $laundries = Laundry::all();
        
        foreach ($laundries as $laundry) {
            // Cek apakah sudah ada owner dengan nama_laundry yang sama
            $existingOwner = Owner::where('nama_laundry', $laundry->nama)->first();
            
            if (!$existingOwner) {
                // Buat owner baru
                $username = Str::slug($laundry->nama) . '_owner';
                $email = Str::slug($laundry->nama) . '@example.com';
                
                $owner = Owner::create([
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'nama_laundry' => $laundry->nama,
                ]);
                
                $this->command->info("Owner baru dibuat untuk {$laundry->nama} dengan ID: {$owner->id}");
            } else {
                $this->command->info("Owner sudah ada untuk {$laundry->nama} dengan ID: {$existingOwner->id}");
            }
        }
        
        $this->command->info('Selesai membuat owner dari data laundry.');
    }
}
