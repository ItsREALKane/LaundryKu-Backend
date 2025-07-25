<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Pesanan::with(['owner', 'admin']);

            // Filter by id_owner if provided
            if ($request->has('id_owner')) {
                $query->where('id_owner', $request->id_owner);
            }

            // Filter by status if provided
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
                'id_owner' => 'required|exists:owners,id',
                'id_admin' => 'nullable|exists:admins,id', // admin yang membuat pesanan
                'nama_pelanggan' => 'required|string|max:255',
                'nomor' => 'required|string|max:20',
                'alamat' => 'required|string|max:500',
                'layanan' => 'required|string|max:255',
                'berat' => 'nullable|numeric|min:0',
                'jumlah_harga' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|in:pending,diproses,selesai',
                'jenis_pembayaran' => 'nullable|in:cash,transfer',
            ]);
            
            // Set default values
            $validatedData['status'] = $validatedData['status'] ?? 'pending';
            
            // id_admin bisa null, tidak perlu diisi otomatis dengan id_owner
            
            $pesanan = Pesanan::create($validatedData);
            
            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $pesanan->load(['owner', 'admin'])
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data yang dimasukkan tidak lengkap/salah!',
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
            $pesanan = Pesanan::with(['owner', 'admin'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $pesanan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Pesanan tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pesanan = Pesanan::findOrFail($id);

            $validatedData = $request->validate([
                'nama_pelanggan' => 'sometimes|required|string|max:255',
                'nomor' => 'sometimes|required|string|max:20',
                'alamat' => 'sometimes|required|string|max:500',
                'layanan' => 'sometimes|required|string|max:255',
                'berat' => 'sometimes|nullable|numeric|min:0',
                'jumlah_harga' => 'sometimes|nullable|numeric|min:0',
                'status' => 'sometimes|required|string|in:pending,diproses,selesai',
                'jenis_pembayaran' => 'sometimes|nullable|in:cash,transfer',
            ]);

            $pesanan->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil diupdate',
                'data' => $pesanan->load(['owner', 'admin'])
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data yang dimasukkan tidak lengkap/salah',
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

    public function destroy($id)
    {
        try {
            $pesanan = Pesanan::findOrFail($id);
            $pesanan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dihapus'
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
