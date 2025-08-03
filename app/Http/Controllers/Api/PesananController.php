<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        try {
            \Log::info('Fetching pesanan with params:', $request->all());
            
            $query = Pesanan::with(['owner', 'admin', 'pelanggan']);

            // Filter by id_owner if provided
            if ($request->has('id_owner')) {
                \Log::info('Filtering by id_owner:', ['id_owner' => $request->id_owner]);
                $query->where('id_owner', $request->id_owner);
            }

            // Filter by status if provided
            if ($request->has('status')) {
                \Log::info('Filtering by status:', ['status' => $request->status]);
                $query->where('status', $request->status);
            }

            $pesanan = $query->latest()->get();
            \Log::info('Found pesanan:', ['count' => $pesanan->count()]);

            return response()->json([
                'status' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $pesanan
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching pesanan:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
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
                'id_admin' => 'nullable|exists:admins,id',
                'id_pelanggan' => 'nullable|exists:pelanggan,id', // Jika menggunakan pelanggan yang sudah ada
                'nama_pelanggan' => 'required_without:id_pelanggan|string|max:255', // Wajib jika tidak ada id_pelanggan
                'nomor' => 'required_without:id_pelanggan|string|max:20', // Wajib jika tidak ada id_pelanggan
                'alamat' => 'required_without:id_pelanggan|string|max:500', // Wajib jika tidak ada id_pelanggan
                'layanan' => 'required|string|max:255',
                'berat' => 'nullable|numeric|min:0',
                'jumlah_harga' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|in:pending,diproses,selesai,lunas',
                'jenis_pembayaran' => 'nullable|in:cash,transfer',
            ]);
            
            // Set default values
            $validatedData['status'] = $validatedData['status'] ?? 'pending';
            
            // Jika menggunakan pelanggan yang sudah ada
            if ($request->has('id_pelanggan')) {
                $pelanggan = Pelanggan::find($request->id_pelanggan);
                if ($pelanggan) {
                    // Ambil data pelanggan untuk disimpan di tabel pesanan
                    $validatedData['nama_pelanggan'] = $pelanggan->nama_pelanggan;
                    $validatedData['nomor'] = $pelanggan->nomor;
                    $validatedData['alamat'] = $pelanggan->alamat;
                }
            } else {
                // Jika menggunakan pelanggan baru, cek apakah sudah ada berdasarkan nomor
                $existingPelanggan = Pelanggan::where('nomor', $request->nomor)->first();
                
                if ($existingPelanggan) {
                    // Gunakan pelanggan yang sudah ada
                    $validatedData['id_pelanggan'] = $existingPelanggan->id;
                    $validatedData['nama_pelanggan'] = $existingPelanggan->nama_pelanggan;
                    $validatedData['nomor'] = $existingPelanggan->nomor;
                    $validatedData['alamat'] = $existingPelanggan->alamat;
                } else {
                    // Buat pelanggan baru di tabel pelanggan
                    $pelanggan = Pelanggan::create([
                        'nama_pelanggan' => $request->nama_pelanggan,
                        'nomor' => $request->nomor,
                        'alamat' => $request->alamat,
                    ]);
                    $validatedData['id_pelanggan'] = $pelanggan->id;
                    // Data pelanggan sudah ada di validatedData
                }
            }
            
            $pesanan = Pesanan::create($validatedData);
            
            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $pesanan->load(['owner', 'admin', 'pelanggan'])
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
            $pesanan = Pesanan::with(['owner', 'admin', 'pelanggan'])
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
                'id_pelanggan' => 'sometimes|exists:pelanggan,id',
                'nama_pelanggan' => 'sometimes|string|max:255',
                'nomor' => 'sometimes|string|max:20',
                'alamat' => 'sometimes|string|max:500',
                'layanan' => 'sometimes|required|string|max:255',
                'berat' => 'sometimes|nullable|numeric|min:0',
                'jumlah_harga' => 'sometimes|nullable|numeric|min:0',
                'status' => 'sometimes|required|string|in:pending,diproses,selesai,lunas',
                'jenis_pembayaran' => 'sometimes|nullable|in:cash,transfer',
            ]);

            // Jika mengubah pelanggan, update data pelanggan di tabel pesanan
            if ($request->has('id_pelanggan')) {
                $pelanggan = Pelanggan::find($request->id_pelanggan);
                if ($pelanggan) {
                    $validatedData['nama_pelanggan'] = $pelanggan->nama_pelanggan;
                    $validatedData['nomor'] = $pelanggan->nomor;
                    $validatedData['alamat'] = $pelanggan->alamat;
                }
            }

            $pesanan->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil diupdate',
                'data' => $pesanan->load(['owner', 'admin', 'pelanggan'])
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
