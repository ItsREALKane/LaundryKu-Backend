<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!$request->has('id_laundry')) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID Laundry teu kapanggih'
                ], 400);
            }

            $id_laundry = $request->id_laundry;

            // Total pendapatan
            $total_pendapatan = Pesanan::where('id_laundry', $id_laundry)
                ->where('status', 'selesai')
                ->sum('total_harga');

            // Total pesanan
            $total_pesanan = Pesanan::where('id_laundry', $id_laundry)->count();

            // Total pelanggan unik
            $total_pelanggan = Pesanan::where('id_laundry', $id_laundry)
                ->distinct('id_user')
                ->count('id_user');

            // Data pesanan per bulan
            $pesanan_per_bulan = Pesanan::where('id_laundry', $id_laundry)
                ->select(
                    DB::raw('DATE_FORMAT(tanggal_pesanan, "%M") as bulan'),
                    DB::raw('COUNT(*) as jumlah')
                )
                ->groupBy('bulan')
                ->orderBy(DB::raw('MONTH(tanggal_pesanan)'))
                ->get();

            // Data pendapatan per bulan
            $pendapatan_per_bulan = Pesanan::where('id_laundry', $id_laundry)
                ->where('status', 'selesai')
                ->select(
                    DB::raw('DATE_FORMAT(tanggal_pesanan, "%M") as bulan'),
                    DB::raw('SUM(total_harga) as jumlah')
                )
                ->groupBy('bulan')
                ->orderBy(DB::raw('MONTH(tanggal_pesanan)'))
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Data statistik berhasil didapatkan',
                'data' => [
                    'total_pendapatan' => (int)$total_pendapatan,
                    'total_pesanan' => $total_pesanan,
                    'total_pelanggan' => $total_pelanggan,
                    'pesanan_per_bulan' => $pesanan_per_bulan,
                    'pendapatan_per_bulan' => $pendapatan_per_bulan
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal nyandak data statistik: ' . $e->getMessage()
            ], 500);
        }
    }
}