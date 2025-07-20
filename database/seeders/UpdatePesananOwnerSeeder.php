<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\Owner;
use App\Models\Laundry;
use Illuminate\Support\Facades\DB;

class UpdatePesananOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi kolom id_owner pada tabel pesanan berdasarkan relasi laundry
     */
    public function run(): void
    {
        // Ambil semua pesanan yang belum memiliki id_owner
        $pesanan = Pesanan::whereNull('id_owner')->get();
        
        foreach ($pesanan as $p) {
            // Cari laundry terkait
            $laundry = Laundry::find($p->id_laundry);
            
            if ($laundry) {
                // Cari owner berdasarkan nama_laundry
                $owner = Owner::where('nama_laundry', $laundry->nama)->first();
                
                if ($owner) {
                    // Update id_owner pada pesanan
                    $p->id_owner = $owner->id;
                    $p->save();
                    
                    $this->command->info("Pesanan ID: {$p->id} diperbarui dengan Owner ID: {$owner->id}");
                } else {
                    $this->command->warn("Owner tidak ditemukan untuk Laundry: {$laundry->nama}");
                }
            } else {
                $this->command->warn("Laundry tidak ditemukan untuk Pesanan ID: {$p->id}");
            }
        }
        
        $this->command->info('Selesai memperbarui relasi pesanan ke owner.');
    }
}
