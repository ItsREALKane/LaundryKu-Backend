<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use App\Models\Layanan;
use App\Models\Owner;

class PelangganLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil owner pertama atau buat owner baru jika tidak ada
        $owner = Owner::first();
        
        if (!$owner) {
            $owner = Owner::create([
                'nama' => 'Owner LaundryKu',
                'email' => 'owner@laundryku.com',
                'password' => bcrypt('password'),
                'nomor' => '081234567890',
                'alamat' => 'Jl. Contoh No. 123',
            ]);
        }

        // Seed data pelanggan
        $pelanggan = [
            [
                'nama_pelanggan' => 'John Doe',
                'nomor' => '081234567890',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta',
            ],
            [
                'nama_pelanggan' => 'Jane Smith',
                'nomor' => '081234567891',
                'alamat' => 'Jl. Thamrin No. 456, Jakarta',
            ],
            [
                'nama_pelanggan' => 'Bob Johnson',
                'nomor' => '081234567892',
                'alamat' => 'Jl. Gatot Subroto No. 789, Jakarta',
            ],
            [
                'nama_pelanggan' => 'Alice Brown',
                'nomor' => '081234567893',
                'alamat' => 'Jl. Rasuna Said No. 321, Jakarta',
            ],
            [
                'nama_pelanggan' => 'Charlie Wilson',
                'nomor' => '081234567894',
                'alamat' => 'Jl. Kuningan No. 654, Jakarta',
            ],
        ];

        foreach ($pelanggan as $data) {
            Pelanggan::create($data);
        }

        // Seed data layanan
        $layanan = [
            [
                'nama_layanan' => 'Cuci Reguler',
                'harga_layanan' => '15000',
                'keterangan_layanan' => 'Cuci biasa dengan waktu pengerjaan 2-3 hari',
                'id_owner' => $owner->id,
            ],
            [
                'nama_layanan' => 'Cuci Express',
                'harga_layanan' => '25000',
                'keterangan_layanan' => 'Cuci cepat dengan waktu pengerjaan 1 hari',
                'id_owner' => $owner->id,
            ],
            [
                'nama_layanan' => 'Cuci Kilat',
                'harga_layanan' => '35000',
                'keterangan_layanan' => 'Cuci sangat cepat dengan waktu pengerjaan 6-8 jam',
                'id_owner' => $owner->id,
            ],
            [
                'nama_layanan' => 'Setrika Saja',
                'harga_layanan' => '8000',
                'keterangan_layanan' => 'Hanya setrika tanpa cuci',
                'id_owner' => $owner->id,
            ],
            [
                'nama_layanan' => 'Cuci + Setrika',
                'harga_layanan' => '20000',
                'keterangan_layanan' => 'Cuci dan setrika dengan waktu pengerjaan 2-3 hari',
                'id_owner' => $owner->id,
            ],
        ];

        foreach ($layanan as $data) {
            Layanan::create($data);
        }

        $this->command->info('Data pelanggan dan layanan berhasil di-seed!');
    }
} 