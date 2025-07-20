<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = [
            [
                'username' => 'owner1',
                'email' => 'owner1@example.com',
                'password' => Hash::make('password123'),
                'nama_laundry' => 'Laundry Bersih Sejahtera',
            ],
            [
                'username' => 'owner2',
                'email' => 'owner2@example.com',
                'password' => Hash::make('password123'),
                'nama_laundry' => 'Laundry Express 24 Jam',
            ],
            [
                'username' => 'owner3',
                'email' => 'owner3@example.com',
                'password' => Hash::make('password123'),
                'nama_laundry' => 'Laundry Kilat Bandung',
            ],
        ];

        foreach ($owners as $owner) {
            Owner::create($owner);
        }
    }
}
