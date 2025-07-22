<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Pesanan::query();


            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $pesanan = $query->latest()->get();

            return response()->json([
                'status' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $pesanan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! ada masalah, coba lagi!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|string|in:pending,proses,selesai,batal',
                'total_harga' => 'required|numeric|min:0',
                'alamat' => 'required|string|max:255',
                'catatan' => 'nullable|string|max:500',
                'jenis_pembayaran' => 'required|in:sekali,langganan',
                'berat' => 'required|numeric|min:0',
                'layanan' => 'required|string',
            ]);

            $pesanan = Pesanan::create($validatedData);
            
            return response()->json([
                'status' => true,
                'message' => 'Pesanan dibuat',
                'data' => $pesanan->load('user:id,name,phone')
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
            $pesanan = Pesanan::with(['detailPesanan', 'tagihan'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $pesanan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Pesanan teu kapanggih',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pesanan = Pesanan::findOrFail($id);

            $validatedData = $request->validate([
                'status' => 'sometimes|required|string|in:pending,proses,selesai,batal',
                'total_harga' => 'sometimes|required|numeric|min:0',
                'catatan' => 'nullable|string|max:500',
            ]);

            $pesanan->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Pesanan geus diupdate',
                'data' => $pesanan->load('user:id,name,phone')
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
            $pesanan = Pesanan::findOrFail($id);
            $pesanan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Pesanan geus dihapus'
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
