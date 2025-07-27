<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    /**
     * Mendapatkan daftar pelanggan berdasarkan id_owner
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'id_owner' => 'required|exists:owners,id'
            ]);

            $id_owner = $request->id_owner;

            // Mengambil data pelanggan unik berdasarkan nomor telepon
            // Menggunakan distinct dan groupBy untuk menghindari duplikasi
            $pelanggan = Pesanan::select('nama_pelanggan', 'nomor', 'alamat')
                ->where('id_owner', $id_owner)
                ->groupBy('nomor', 'nama_pelanggan', 'alamat')
                ->orderBy('nama_pelanggan')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Daftar pelanggan berhasil diambil',
                'data' => $pelanggan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ada masalah, coba lagi!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari data pelanggan berdasarkan nomor telepon
     */
    public function findByNomor(Request $request)
    {
        try {
            $request->validate([
                'nomor' => 'required|string',
                'id_owner' => 'required|exists:owners,id'
            ]);

            $nomor = $request->nomor;
            $id_owner = $request->id_owner;

            // Cari pesanan terakhir dengan nomor telepon tersebut untuk owner yang sama
            $pesanan = Pesanan::where('nomor', $nomor)
                ->where('id_owner', $id_owner)
                ->latest()
                ->first();

            if (!$pesanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data pelanggan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data pelanggan ditemukan',
                'data' => [
                    'nama_pelanggan' => $pesanan->nama_pelanggan,
                    'nomor' => $pesanan->nomor,
                    'alamat' => $pesanan->alamat
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ada masalah, coba lagi!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}