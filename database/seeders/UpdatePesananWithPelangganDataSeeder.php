<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

class UpdatePesananWithPelangganDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua pesanan yang memiliki nama_pelanggan kosong
        $pesananKosong = Pesanan::where('nama_pelanggan', '')
            ->orWhereNull('nama_pelanggan')
            ->orWhere('nama_pelanggan', '=', null)
            ->get();

        echo "Found " . $pesananKosong->count() . " pesanan with empty customer data\n";

        foreach ($pesananKosong as $pesanan) {
            // Ambil pelanggan pertama yang tersedia
            $pelanggan = Pelanggan::first();
            
            if ($pelanggan) {
                // Update pesanan dengan data pelanggan
                $pesanan->update([
                    'nama_pelanggan' => $pelanggan->nama_pelanggan,
                    'nomor' => $pelanggan->nomor,
                    'alamat' => $pelanggan->alamat,
                    'id_pelanggan' => $pelanggan->id,
                ]);
                
                echo "Updated pesanan ID: " . $pesanan->id . " with customer: " . $pelanggan->nama_pelanggan . "\n";
            }
        }

        // Jika masih ada pesanan kosong, isi dengan data dummy
        $pesananKosong = Pesanan::where('nama_pelanggan', '')
            ->orWhereNull('nama_pelanggan')
            ->orWhere('nama_pelanggan', '=', null)
            ->get();

        if ($pesananKosong->count() > 0) {
            echo "Still have " . $pesananKosong->count() . " pesanan with empty data, filling with dummy data\n";
            
            $dummyCustomers = [
                ['nama' => 'John Doe', 'nomor' => '081234567890', 'alamat' => 'Jl. Sudirman No. 123, Jakarta'],
                ['nama' => 'Jane Smith', 'nomor' => '081234567891', 'alamat' => 'Jl. Thamrin No. 456, Jakarta'],
                ['nama' => 'Bob Johnson', 'nomor' => '081234567892', 'alamat' => 'Jl. Gatot Subroto No. 789, Jakarta'],
                ['nama' => 'Alice Brown', 'nomor' => '081234567893', 'alamat' => 'Jl. Rasuna Said No. 321, Jakarta'],
                ['nama' => 'Charlie Wilson', 'nomor' => '081234567894', 'alamat' => 'Jl. Kuningan No. 654, Jakarta'],
            ];

            $index = 0;
            foreach ($pesananKosong as $pesanan) {
                $customer = $dummyCustomers[$index % count($dummyCustomers)];
                
                $pesanan->update([
                    'nama_pelanggan' => $customer['nama'],
                    'nomor' => $customer['nomor'],
                    'alamat' => $customer['alamat'],
                ]);
                
                echo "Updated pesanan ID: " . $pesanan->id . " with dummy customer: " . $customer['nama'] . "\n";
                $index++;
            }
        }

        echo "Seeder completed!\n";
    }
} 