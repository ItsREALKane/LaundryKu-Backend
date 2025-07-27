<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pesanan;
use App\Models\DetailTagihan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Tagihan::with(['owner:id,username,nama_laundry', 'detailTagihan']);

            if ($request->has('id_owner')) {
                $query->where('id_owner', $request->id_owner);
            }

            // Filter berdasarkan status sudah tidak ada lagi di model Tagihan baru

            if ($request->has('tanggal_mulai') && $request->has('tanggal_akhir')) {
                $query->whereBetween('created_at', [$request->tanggal_mulai, $request->tanggal_akhir]);
            }

            $tagihan = $query->latest()->get();

            return response()->json([
                'status' => true,
                'message' => 'Data tagihan berhasil diambil',
                'data' => $tagihan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ada masalah, coba lagi!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_pelanggan' => 'required|string',
                'nomor' => 'required|string',
                'alamat' => 'required|string',
                'jumlah_pesanan' => 'required|integer|min:1',
                'total_tagihan' => 'required|numeric|min:0',
                'id_owner' => 'required|exists:owners,id',
            ]);

            // Cek apakah pelanggan dengan nama tersebut memiliki pesanan yang belum lunas
            $detailTagihan = DetailTagihan::where('nama_pelanggan', $validatedData['nama_pelanggan'])
                ->where('id_owner', $validatedData['id_owner'])
                ->where('status', '!=', 'lunas')
                ->get();

            if ($detailTagihan->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada pesanan yang belum lunas untuk pelanggan ini!'
                ], 422);
            }

            // Verifikasi jumlah pesanan dan total tagihan
            if ($validatedData['jumlah_pesanan'] != $detailTagihan->count()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jumlah pesanan tidak sesuai dengan data yang ada!'
                ], 422);
            }

            if ($validatedData['total_tagihan'] != $detailTagihan->sum('jumlah_harga')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Total tagihan tidak sesuai dengan total harga pesanan!'
                ], 422);
            }

            $tagihan = Tagihan::create($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Tagihan sudah dibuat',
                'data' => $tagihan->load(['owner:id,username,nama_laundry', 'detailTagihan'])
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data yang dimasukkeun tidak lengkap/salah',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ada masalah, coba lagi!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $tagihan = Tagihan::with(['owner:id,username,nama_laundry', 'detailTagihan'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data tagihan berhasil diambil',
                'data' => $tagihan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Tagihan tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tagihan = Tagihan::findOrFail($id);

            $validatedData = $request->validate([
                'nama_pelanggan' => 'sometimes|required|string',
                'nomor' => 'sometimes|required|string',
                'alamat' => 'sometimes|required|string',
                'jumlah_pesanan' => 'sometimes|required|integer|min:1',
                'total_tagihan' => 'sometimes|required|numeric|min:0',
            ]);

            // Verifikasi data jika ada perubahan pada jumlah pesanan atau total tagihan
            if (isset($validatedData['jumlah_pesanan']) || isset($validatedData['total_tagihan'])) {
                $detailTagihan = $tagihan->detailTagihan;

                if (isset($validatedData['jumlah_pesanan']) && $validatedData['jumlah_pesanan'] != $detailTagihan->count()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Jumlah pesanan tidak sesuai dengan data yang ada!'
                    ], 422);
                }

                if (isset($validatedData['total_tagihan']) && $validatedData['total_tagihan'] != $detailTagihan->sum('jumlah_harga')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Total tagihan tidak sesuai dengan total harga pesanan!'
                    ], 422);
                }
            }

            $tagihan->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Tagihan geus diupdate',
                'data' => $tagihan->load(['owner:id,username,nama_laundry', 'detailTagihan'])
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Data nu dimasukkeun teu lengkap/salah',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Aya masalah, coba deui nya!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPesananSiapDitagih(Request $request)
    {
        $id_owner = $request->query('id_owner');

        if (!$id_owner) {
            return response()->json([
                'status' => false,
                'message' => 'ID owner wajib diisi',
            ], 400);
        }

        // Ambil semua pesanan dengan status selesai dan lunas untuk owner tertentu
        $pesanan = Pesanan::with('owner')
            ->where('id_owner', $id_owner)
            ->whereIn('status', ['selesai', 'lunas'])
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Pesanan siap ditagih berhasil diambil',
            'data' => $pesanan
        ]);
    }



    public function belumBayar(Request $request)
    {
        try {
            $id_owner = $request->query('id_owner');
            if (!$id_owner) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID Owner tidak ditemukan!'
                ], 400);
            }

            // Ambil detail tagihan dengan status "selesai" tapi belum masuk tagihan (misalnya belum dibuat tagihannya)
            $detailTagihan = DetailTagihan::where('id_owner', $id_owner)
                ->where('status', 'selesai')
                ->get()
                ->groupBy('nama_pelanggan');

            $data = [];

            foreach ($detailTagihan as $nama => $group) {
                $data[] = [
                    'nama_pelanggan' => $nama,
                    'jumlah_tagihan' => $group->count(),
                    'total_tagihan' => $group->sum('jumlah_harga'),
                    'detail_tagihan' => $group->values(),
                ];
            }

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal ambil data tagihan belum bayar',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $tagihan = Tagihan::findOrFail($id);

            // Tidak perlu update status pesanan karena tidak ada relasi langsung dengan pesanan
            // Detail tagihan tetap ada dan tidak terpengaruh oleh penghapusan tagihan

            $tagihan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Tagihan geus dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Aya masalah, coba deui nya!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
