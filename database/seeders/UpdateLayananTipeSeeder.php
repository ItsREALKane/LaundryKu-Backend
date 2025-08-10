<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layanan;

class UpdateLayananTipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update semua layanan yang belum memiliki tipe
        $layananWithoutTipe = Layanan::whereNull('tipe')->get();
        
        foreach ($layananWithoutTipe as $layanan) {
            // Tentukan tipe berdasarkan nama layanan
            $tipe = 'Kiloan'; // default
            
            // Jika layanan mengandung kata "setrika" atau "satuan", maka tipe = Satuan
            if (stripos($layanan->nama_layanan, 'setrika') !== false) {
                $tipe = 'Satuan';
            }
            
            $layanan->update(['tipe' => $tipe]);
        }
        
        $this->command->info('Data layanan berhasil diperbarui dengan kolom tipe!');
    }
}
