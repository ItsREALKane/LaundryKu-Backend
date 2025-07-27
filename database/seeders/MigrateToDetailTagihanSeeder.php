<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\DetailTagihan;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;

class MigrateToDetailTagihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate tables to avoid duplicates
        DB::table('detail_tagihan')->truncate();
        DB::table('tagihan')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Get all pesanan
        $pesanan = Pesanan::all();
        
        // Group pesanan by owner and customer
        $groupedPesanan = $pesanan->groupBy(['id_owner', 'nama_pelanggan']);
        
        // Create tagihan and detail_tagihan records
        foreach ($groupedPesanan as $ownerId => $ownerGroup) {
            foreach ($ownerGroup as $customerName => $customerPesanan) {
                // Skip if no pesanan
                if ($customerPesanan->isEmpty()) {
                    continue;
                }
                
                // Get first pesanan for customer info
                $firstPesanan = $customerPesanan->first();
                
                // Count total pesanan
                $jumlahPesanan = $customerPesanan->count();
                
                // Calculate total tagihan (only for non-lunas pesanan)
                $totalTagihan = $customerPesanan
                    ->where('status', '!=', 'lunas')
                    ->sum('jumlah_harga');
                
                // Create tagihan record
                $tagihan = new Tagihan([
                    'nama_pelanggan' => $customerName,
                    'nomor' => $firstPesanan->nomor,
                    'alamat' => $firstPesanan->alamat,
                    'jumlah_pesanan' => $jumlahPesanan,
                    'total_tagihan' => $totalTagihan,
                    'id_owner' => $ownerId
                ]);
                
                $tagihan->save();
                
                // Create detail_tagihan records for each pesanan
                foreach ($customerPesanan as $pesanan) {
                    $detailTagihan = new DetailTagihan([
                        'id_pesanan' => $pesanan->id,
                        'layanan' => $pesanan->layanan,
                        'berat' => $pesanan->berat,
                        'jumlah_harga' => $pesanan->jumlah_harga,
                        'status' => $pesanan->status,
                        'id_owner' => $pesanan->id_owner,
                        'nama_pelanggan' => $pesanan->nama_pelanggan
                    ]);
                    
                    $detailTagihan->save();
                }
            }
        }
        
        $this->command->info('Successfully migrated data to detail_tagihan and tagihan tables!');
    }
}