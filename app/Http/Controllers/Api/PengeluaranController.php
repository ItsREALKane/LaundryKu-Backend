<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $id_owner = $request->user()->id;
            
            $query = Pengeluaran::where('id_owner', $id_owner);
            
            // Filter berdasarkan bulan dan tahun jika ada
            if ($request->has('bulan') && $request->has('tahun')) {
                $bulan = $request->bulan;
                $tahun = $request->tahun;
                $query->whereMonth('tanggal', $bulan)
                      ->whereYear('tanggal', $tahun);
            }
            
            // Filter berdasarkan kategori jika ada
            if ($request->has('kategori')) {
                $query->where('kategori', $request->kategori);
            }
            
            $pengeluaran = $query->orderBy('tanggal', 'desc')->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Data pengeluaran berhasil diambil',
                'data' => $pengeluaran
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Kasalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kategori' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string',
                'tanggal' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pengeluaran = Pengeluaran::create([
                'id_owner' => $request->user()->id,
                'kategori' => $request->kategori,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'tanggal' => $request->tanggal,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Pengeluaran berhasil ditambahkan',
                'data' => $pengeluaran
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Kasalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        try {
            $pengeluaran = Pengeluaran::where('id', $id)
                ->where('id_owner', $request->user()->id)
                ->first();

            if (!$pengeluaran) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pengeluaran tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Detail pengeluaran',
                'data' => $pengeluaran
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Kasalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $pengeluaran = Pengeluaran::where('id', $id)
                ->where('id_owner', $request->user()->id)
                ->first();

            if (!$pengeluaran) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pengeluaran tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'kategori' => 'sometimes|string|max:255',
                'jumlah' => 'sometimes|numeric|min:0',
                'keterangan' => 'nullable|string',
                'tanggal' => 'sometimes|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only(['kategori', 'jumlah', 'keterangan', 'tanggal']);
            $pengeluaran->update($updateData);

            return response()->json([
                'status' => true,
                'message' => 'Pengeluaran berhasil diperbarui',
                'data' => $pengeluaran->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Kasalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        try {
            $pengeluaran = Pengeluaran::where('id', $id)
                ->where('id_owner', $request->user()->id)
                ->first();

            if (!$pengeluaran) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pengeluaran tidak ditemukan'
                ], 404);
            }

            $pengeluaran->delete();

            return response()->json([
                'status' => true,
                'message' => 'Pengeluaran berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Kasalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get laporan keuangan bulanan
     */
    public function getLaporanBulanan(Request $request)
    {
        try {
            $id_owner = $request->user()->id;
            $tahun = $request->tahun ?? date('Y');
            
            // Ambil data pendapatan per bulan (dari pesanan yang sudah lunas)
            $pendapatan = DB::table('pesanan')
                ->where('id_owner', $id_owner)
                ->where('status', 'lunas') // Ubah dari 'selesai' menjadi 'lunas'
                ->whereYear('created_at', $tahun)
                ->select(
                    DB::raw('MONTH(created_at) as bulan'),
                    DB::raw('SUM(jumlah_harga) as total_pendapatan')
                )
                ->groupBy('bulan')
                ->get()
                ->keyBy('bulan');
            
            // Ambil data pengeluaran per bulan
            $pengeluaran = DB::table('pengeluaran')
                ->where('id_owner', $id_owner)
                ->whereYear('tanggal', $tahun)
                ->select(
                    DB::raw('MONTH(tanggal) as bulan'),
                    DB::raw('SUM(jumlah) as total_pengeluaran')
                )
                ->groupBy('bulan')
                ->get()
                ->keyBy('bulan');
            
            // Gabungkan data pendapatan dan pengeluaran
            $laporan = [];
            for ($i = 1; $i <= 12; $i++) {
                $bulan = date('F', mktime(0, 0, 0, $i, 1));
                $total_pendapatan = isset($pendapatan[$i]) ? $pendapatan[$i]->total_pendapatan : 0;
                $total_pengeluaran = isset($pengeluaran[$i]) ? $pengeluaran[$i]->total_pengeluaran : 0;
                $laba = $total_pendapatan - $total_pengeluaran;
                
                $laporan[] = [
                    'bulan' => $i,
                    'nama_bulan' => $bulan,
                    'pendapatan' => $total_pendapatan,
                    'pengeluaran' => $total_pengeluaran,
                    'laba' => $laba
                ];
            }
            
            // Hitung total keseluruhan
            $total_pendapatan = array_sum(array_column($laporan, 'pendapatan'));
            $total_pengeluaran = array_sum(array_column($laporan, 'pengeluaran'));
            $total_laba = $total_pendapatan - $total_pengeluaran;
            
            return response()->json([
                'status' => true,
                'message' => 'Laporan keuangan berhasil diambil',
                'data' => [
                    'tahun' => $tahun,
                    'laporan_bulanan' => $laporan,
                    'total' => [
                        'pendapatan' => $total_pendapatan,
                        'pengeluaran' => $total_pengeluaran,
                        'laba' => $total_laba
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Kasalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get kategori pengeluaran
     */
    public function getKategori(Request $request)
    {
        try {
            $id_owner = $request->user()->id;
            
            $kategori = Pengeluaran::where('id_owner', $id_owner)
                ->select('kategori')
                ->distinct()
                ->get()
                ->pluck('kategori');
            
            return response()->json([
                'status' => true,
                'message' => 'Kategori pengeluaran berhasil diambil',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Kasalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}