<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $id_owner = $request->query('id_owner');
            $adminId = $request->query('id_admin');
            
            // Ambil data pelanggan dari tabel pelanggan
            $query = Pelanggan::with('owner:id,username,nama_laundry,email')
                ->select('id', 'nama_pelanggan', 'nomor', 'alamat', 'id_owner', 'created_at', 'updated_at');
            
            if ($id_owner) {
                $query->where('id_owner', $id_owner);
            }
            
            // Filter berdasarkan admin jika ada
            if ($adminId) {
                $query->whereHas('owner.admins', function($q) use ($adminId) {
                    $q->where('id', $adminId);
                });
            }
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_pelanggan', 'like', "%{$search}%")
                      ->orWhere('nomor', 'like', "%{$search}%");
                });
            }
            
            $pelanggan = $query->orderBy('nama_pelanggan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
                'nama_pelanggan' => 'required|string|max:255',
                'nomor' => 'required|string|max:255',
                'alamat' => 'required|string',
                'id_owner' => 'required|exists:owners,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek apakah pelanggan sudah ada berdasarkan nomor dan owner
            $existingPelanggan = Pelanggan::where('nomor', $request->nomor)
                ->where('id_owner', $request->id_owner)
                ->first();
            
            if ($existingPelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan dengan nomor tersebut sudah terdaftar',
                    'data' => $existingPelanggan
                ], 409);
            }

            $pelanggan = Pelanggan::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan',
                'data' => $pelanggan
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $pelanggan = Pelanggan::with(['pesanan', 'owner:id,username,nama_laundry,email'])->find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_pelanggan' => 'sometimes|required|string|max:255',
                'nomor' => 'sometimes|required|string|max:255',
                'alamat' => 'sometimes|required|string',
                'id_owner' => 'sometimes|required|exists:owners,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek apakah nomor sudah digunakan oleh pelanggan lain dalam owner yang sama
            if ($request->has('nomor') && $request->nomor !== $pelanggan->nomor) {
                $existingPelanggan = Pelanggan::where('nomor', $request->nomor)
                    ->where('id_owner', $request->id_owner ?? $pelanggan->id_owner)
                    ->where('id', '!=', $id)
                    ->first();
                
                if ($existingPelanggan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor sudah digunakan oleh pelanggan lain'
                    ], 409);
                }
            }

            $pelanggan->update($request->all());

            // Load relasi owner untuk response
            $pelanggan->load('owner:id,username,nama_laundry,email');

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil diperbarui',
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }

            // Cek apakah pelanggan memiliki pesanan
            if ($pelanggan->pesanan()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak dapat dihapus karena memiliki pesanan'
                ], 400);
            }

            $pelanggan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search pelanggan by name or number
     */
    public function search(Request $request)
    {
        try {
            $search = $request->query('q');
            $id_owner = $request->query('id_owner');
            $adminId = $request->query('id_admin');
            
            if (!$search) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter pencarian diperlukan'
                ], 400);
            }

            // Cari pelanggan dari tabel pelanggan
            $query = Pelanggan::with('owner:id,username,nama_laundry,email')
                ->select('id', 'nama_pelanggan', 'nomor', 'alamat', 'id_owner', 'created_at', 'updated_at')
                ->where(function($q) use ($search) {
                    $q->where('nama_pelanggan', 'like', "%{$search}%")
                      ->orWhere('nomor', 'like', "%{$search}%");
                });
            
            if ($id_owner) {
                $query->where('id_owner', $id_owner);
            }

            // Filter berdasarkan admin jika ada
            if ($adminId) {
                $query->whereHas('owner.admins', function($q) use ($adminId) {
                    $q->where('id', $adminId);
                });
            }

            $pelanggan = $query->orderBy('nama_pelanggan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pelanggan by admin
     */
    public function getByAdmin(Request $request, $adminId)
    {
        try {
            $search = $request->query('search');
            
            $query = Pelanggan::with('owner:id,username,nama_laundry,email')
                ->select('id', 'nama_pelanggan', 'nomor', 'alamat', 'id_owner', 'created_at', 'updated_at')
                ->whereHas('owner.admins', function($q) use ($adminId) {
                    $q->where('id', $adminId);
                });

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_pelanggan', 'like', "%{$search}%")
                      ->orWhere('nomor', 'like', "%{$search}%");
                });
            }

            $pelanggan = $query->orderBy('nama_pelanggan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}