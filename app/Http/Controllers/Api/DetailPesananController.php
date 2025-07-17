<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DetailPesananController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DetailPesanan::with(['pesanan']);
            
            if ($request->has('id_pesanan')) {
                $query->where('id_pesanan', $request->id_pesanan);
            }

            $detailPesanan = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Data detail pesanan berhasil diambil',
                'data' => $detailPesanan
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
                'id_pesanan' => 'required|exists:pesanan,id',
                'pesanan' => 'required|string|max:255',
                'harga_pesanan' => 'required|numeric|min:0',
                'total_pesanan' => 'required|numeric|min:1',
            ]);

            // Cek heula pesanan na aya teu
            $pesanan = Pesanan::findOrFail($validatedData['id_pesanan']);

            $detailPesanan = DetailPesanan::create($validatedData);
            
            // Update total harga di pesanan
            $totalHarga = $validatedData['harga_pesanan'] * $validatedData['total_pesanan'];
            $pesanan->update([
                'total_harga' => $pesanan->total_harga + $totalHarga
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Detail pesanan geus dijieun',
                'data' => $detailPesanan->load('pesanan')
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
            $detailPesanan = DetailPesanan::with('pesanan')
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data detail pesanan berhasil diambil',
                'data' => $detailPesanan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Detail pesanan teu kapanggih',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $detailPesanan = DetailPesanan::findOrFail($id);
            $oldTotal = $detailPesanan->harga_pesanan * $detailPesanan->total_pesanan;

            $validatedData = $request->validate([
                'pesanan' => 'sometimes|required|string|max:255',
                'harga_pesanan' => 'sometimes|required|numeric|min:0',
                'total_pesanan' => 'sometimes|required|numeric|min:1',
            ]);

            $detailPesanan->update($validatedData);

            // Update total harga di pesanan
            if (isset($validatedData['harga_pesanan']) || isset($validatedData['total_pesanan'])) {
                $newTotal = $detailPesanan->harga_pesanan * $detailPesanan->total_pesanan;
                $pesanan = $detailPesanan->pesanan;
                $pesanan->update([
                    'total_harga' => $pesanan->total_harga - $oldTotal + $newTotal
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Detail pesanan geus diupdate',
                'data' => $detailPesanan->load('pesanan')
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
            $detailPesanan = DetailPesanan::findOrFail($id);
            
            // Update total harga di pesanan
            $totalHarga = $detailPesanan->harga_pesanan * $detailPesanan->total_pesanan;
            $pesanan = $detailPesanan->pesanan;
            $pesanan->update([
                'total_harga' => $pesanan->total_harga - $totalHarga
            ]);

            $detailPesanan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Detail pesanan geus dihapus'
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