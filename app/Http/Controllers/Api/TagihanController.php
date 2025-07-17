<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Tagihan::with(['user:id,name,phone', 'laundry:id,nama,alamat', 'pesanan']);
            
            if ($request->has('id_laundry')) {
                $query->where('id_laundry', $request->id_laundry);
            }

            if ($request->has('status_pembayaran')) {
                $query->where('status_pembayaran', $request->status_pembayaran);
            }

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
                'message' => 'Waduh! Aya masalah, coba deui nya!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_user' => 'required|exists:users,id',
                'id_laundry' => 'required|exists:laundry,id',
                'id_pesanan' => 'required|exists:pesanan,id|unique:tagihan,id_pesanan',
                'total_tagihan' => 'required|numeric|min:0',
                'status_pembayaran' => 'required|in:pending,dibayar,batal',
                'metode_pembayaran' => 'required|in:cash,transfer,ewallet',
                'bukti_pembayaran' => 'nullable|string',
                'catatan' => 'nullable|string|max:500',
            ]);

            // Cek heula pesanan na aya teu
            $pesanan = Pesanan::findOrFail($validatedData['id_pesanan']);
            
            // Cek total tagihan sesuai jeung total harga pesanan
            if ($validatedData['total_tagihan'] != $pesanan->total_harga) {
                return response()->json([
                    'status' => false,
                    'message' => 'Total tagihan teu sama jeung total harga pesanan!'
                ], 422);
            }

            $tagihan = Tagihan::create($validatedData);
            
            // Update status pesanan jadi 'dibayar' lamun status_pembayaran = 'dibayar'
            if ($validatedData['status_pembayaran'] === 'dibayar') {
                $pesanan->update(['status' => 'proses']);
            }

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Tagihan geus dijieun',
                'data' => $tagihan->load(['user:id,name,phone', 'pesanan'])
            ], 201);
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

    public function show($id)
    {
        try {
            $tagihan = Tagihan::with(['user:id,name,phone', 'laundry:id,nama,alamat', 'pesanan'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data tagihan berhasil diambil',
                'data' => $tagihan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Tagihan teu kapanggih',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tagihan = Tagihan::findOrFail($id);

            $validatedData = $request->validate([
                'status_pembayaran' => 'sometimes|required|in:pending,dibayar,batal',
                'metode_pembayaran' => 'sometimes|required|in:cash,transfer,ewallet',
                'bukti_pembayaran' => 'nullable|string',
                'catatan' => 'nullable|string|max:500',
            ]);

            $tagihan->update($validatedData);

            // Update status pesanan based on status_pembayaran
            if (isset($validatedData['status_pembayaran'])) {
                $pesanan = $tagihan->pesanan;
                if ($validatedData['status_pembayaran'] === 'dibayar') {
                    $pesanan->update(['status' => 'proses']);
                } elseif ($validatedData['status_pembayaran'] === 'batal') {
                    $pesanan->update(['status' => 'batal']);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Tagihan geus diupdate',
                'data' => $tagihan->load(['user:id,name,phone', 'pesanan'])
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

    public function destroy($id)
    {
        try {
            $tagihan = Tagihan::findOrFail($id);
            
            // Update status pesanan jadi 'batal' lamun tagihan dihapus
            $tagihan->pesanan->update(['status' => 'batal']);
            
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
