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
            
            $query = Pesanan::with(['owner', 'admin', 'pelanggan', 'layanan']);

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
            \Log::info('Creating pesanan with data', ['data' => $request->all()]);
            \Log::info('Request headers', ['headers' => $request->headers->all()]);
            \Log::info('Request method', ['method' => $request->method()]);
            \Log::info('Request URL', ['url' => $request->url()]);
            
            // Validasi tambahan untuk debugging
            if (!$request->has('id_owner')) {
                \Log::error('Missing id_owner in request', ['received_data' => $request->all()]);
                return response()->json([
                    'status' => false,
                    'message' => 'ID Owner tidak ditemukan dalam request',
                    'received_data' => $request->all()
                ], 422);
            }
            
            if (!$request->has('nama_pelanggan')) {
                \Log::error('Missing nama_pelanggan in request', ['received_data' => $request->all()]);
                return response()->json([
                    'status' => false,
                    'message' => 'Nama pelanggan tidak ditemukan dalam request',
                    'received_data' => $request->all()
                ], 422);
            }
            
            \Log::info('Required fields check passed');
            
            $validatedData = $request->validate([
                'id_owner' => 'required|exists:owners,id',
                'id_admin' => 'nullable|exists:admins,id',
                'id_pelanggan' => 'nullable|exists:pelanggan,id', // Jika menggunakan pelanggan yang sudah ada
                'nama_pelanggan' => 'required_without:id_pelanggan|string|max:255', // Wajib jika tidak ada id_pelanggan
                'nomor' => 'required_without:id_pelanggan|string|max:20', // Wajib jika tidak ada id_pelanggan
                'alamat' => 'required_without:id_pelanggan|string|max:500', // Wajib jika tidak ada id_pelanggan
                'id_layanan' => 'required|exists:layanan,id',
                'layanan' => 'nullable|string|max:255', // Sekarang optional karena akan diambil dari tabel layanan
                'berat' => 'nullable|numeric|min:0',
                'banyak_satuan' => 'nullable|numeric|min:0',
                'jumlah_harga' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|in:pending,diproses,selesai,lunas',
                'jenis_pembayaran' => 'nullable|in:cash,transfer',
            ]);
            
            \Log::info('Validation passed, validated data', ['data' => $validatedData]);
            \Log::info('Validation passed, validated data types', [
                'id_owner' => gettype($validatedData['id_owner']),
                'nama_pelanggan' => gettype($validatedData['nama_pelanggan']),
                'nomor' => gettype($validatedData['nomor']),
                'alamat' => gettype($validatedData['alamat']),
                'layanan' => gettype($validatedData['layanan'])
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
            
            // Ambil data layanan dari tabel layanan
            $layanan = \App\Models\Layanan::find($validatedData['id_layanan']);
            if ($layanan) {
                $validatedData['layanan'] = $layanan->nama_layanan;
            }
            
            \Log::info('Final data before creating pesanan', ['data' => $validatedData]);
            
            $pesanan = Pesanan::create($validatedData);
            
            \Log::info('Pesanan created successfully', ['id' => $pesanan->id]);
            
            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $pesanan->load(['owner', 'admin', 'pelanggan', 'layanan'])
            ], 201);
        } catch (ValidationException $e) {
            \Log::error('Validation error', ['errors' => $e->errors()]);
            \Log::error('Validation error details', [
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Data yang dimasukkan tidak lengkap/salah!',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating pesanan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
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
            $pesanan = Pesanan::with(['owner', 'admin', 'pelanggan', 'layanan'])
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
                'id_layanan' => 'sometimes|exists:layanan,id',
                'layanan' => 'sometimes|string|max:255',
                'berat' => 'sometimes|nullable|numeric|min:0',
                'banyak_satuan' => 'sometimes|nullable|numeric|min:0',
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

            // Jika mengubah layanan, update data layanan di tabel pesanan
            if ($request->has('id_layanan')) {
                $layanan = \App\Models\Layanan::find($request->id_layanan);
                if ($layanan) {
                    $validatedData['layanan'] = $layanan->nama_layanan;
                }
            }

            $pesanan->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil diupdate',
                'data' => $pesanan->load(['owner', 'admin', 'pelanggan', 'layanan'])
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

    /**
     * Get layanan by owner for pesanan
     */
    public function getLayananByOwner(Request $request)
    {
        try {
            $ownerId = $request->query('id_owner');
            
            if (!$ownerId) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID Owner diperlukan'
                ], 400);
            }

            $layanan = \App\Models\Layanan::where('id_owner', $ownerId)
                ->select('id', 'nama_layanan', 'harga_layanan', 'keterangan_layanan', 'tipe', 'waktu_pengerjaan')
                ->orderBy('nama_layanan')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Data layanan berhasil diambil',
                'data' => $layanan
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
