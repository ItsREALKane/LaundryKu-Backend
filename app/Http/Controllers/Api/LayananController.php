<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $ownerId = $request->query('id_owner');
            $adminId = $request->query('id_admin');
            
            $query = Layanan::query()
                ->with(['owner:id,username,nama_laundry,email'])
                ->select([
                    'id',
                    'nama_layanan',
                    'harga_layanan',
                    'keterangan_layanan',
                    'tipe',
                    'waktu_pengerjaan',
                    'id_owner',
                    'created_at',
                    'updated_at'
                ]);
            
            // Filter berdasarkan owner jika ada
            if ($ownerId) {
                $query->where('id_owner', $ownerId);
            }
            
            // Filter berdasarkan admin jika ada
            if ($adminId) {
                $query->whereHas('owner.admins', function($q) use ($adminId) {
                    $q->where('id', $adminId);
                });
            }

            $layanan = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $layanan
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
                'nama_layanan' => 'required|string|max:255',
                'harga_layanan' => 'required|string|max:255',
                'keterangan_layanan' => 'required|string',
                'tipe' => 'required|in:Kiloan,Satuan',
                'waktu_pengerjaan' => 'nullable|integer|min:1',
                'id_owner' => 'nullable|exists:owners,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $layanan = Layanan::create($request->all());

            // Load relasi owner untuk response
            $layanan->load('owner:id,username,nama_laundry,email');

            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil ditambahkan',
                'data' => $layanan
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
            $layanan = Layanan::with(['owner:id,username,nama_laundry,email'])
                ->select([
                    'id',
                    'nama_layanan',
                    'harga_layanan',
                    'keterangan_layanan',
                    'tipe',
                    'waktu_pengerjaan',
                    'id_owner',
                    'created_at',
                    'updated_at'
                ])
                ->find($id);

            if (!$layanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $layanan
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
            $layanan = Layanan::find($id);

            if (!$layanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_layanan' => 'sometimes|required|string|max:255',
                'harga_layanan' => 'sometimes|required|string|max:255',
                'keterangan_layanan' => 'sometimes|required|string',
                'tipe' => 'sometimes|required|in:Kiloan,Satuan',
                'waktu_pengerjaan' => 'sometimes|nullable|integer|min:1',
                'id_owner' => 'sometimes|nullable|exists:owners,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $layanan->update($request->all());

            // Load relasi owner untuk response
            $layanan->load('owner:id,username,nama_laundry,email');

            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil diperbarui',
                'data' => $layanan
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
            $layanan = Layanan::find($id);

            if (!$layanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan tidak ditemukan'
                ], 404);
            }

            $layanan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get layanan by admin
     */
    public function getByAdmin(Request $request, $adminId)
    {
        try {
            $query = Layanan::query()
                ->with(['owner:id,username,nama_laundry,email'])
                ->select([
                    'id',
                    'nama_layanan',
                    'harga_layanan',
                    'keterangan_layanan',
                    'tipe',
                    'waktu_pengerjaan',
                    'id_owner',
                    'created_at',
                    'updated_at'
                ])
                ->whereHas('owner.admins', function($q) use ($adminId) {
                    $q->where('id', $adminId);
                });

            $layanan = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $layanan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get layanan statistics
     */
    public function getStats(Request $request)
    {
        try {
            $ownerId = $request->query('id_owner');
            $adminId = $request->query('id_admin');
            
            $query = Layanan::query();
            
            if ($ownerId) {
                $query->where('id_owner', $ownerId);
            }
            
            if ($adminId) {
                $query->whereHas('owner.admins', function($q) use ($adminId) {
                    $q->where('id', $adminId);
                });
            }

            $totalLayanan = $query->count();
            $layananTerbaru = $query->latest()->take(5)->get();
            
            // Statistik berdasarkan owner
            $statsByOwner = $query->with('owner:id,username,nama_laundry')
                ->get()
                ->groupBy('id_owner')
                ->map(function($layanan) {
                    return [
                        'owner' => $layanan->first()->owner,
                        'jumlah_layanan' => $layanan->count()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_layanan' => $totalLayanan,
                    'layanan_terbaru' => $layananTerbaru,
                    'stats_by_owner' => $statsByOwner
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get layanan by tipe
     */
    public function getByTipe(Request $request)
    {
        try {
            $tipe = $request->query('tipe');
            $ownerId = $request->query('id_owner');
            $adminId = $request->query('id_admin');
            
            if (!$tipe || !in_array($tipe, ['Kiloan', 'Satuan'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe harus diisi dengan "Kiloan" atau "Satuan"'
                ], 400);
            }
            
            $query = Layanan::query()
                ->with(['owner:id,username,nama_laundry,email'])
                ->select([
                    'id',
                    'nama_layanan',
                    'harga_layanan',
                    'keterangan_layanan',
                    'tipe',
                    'waktu_pengerjaan',
                    'id_owner',
                    'created_at',
                    'updated_at'
                ])
                ->where('tipe', $tipe);
            
            // Filter berdasarkan owner jika ada
            if ($ownerId) {
                $query->where('id_owner', $ownerId);
            }
            
            // Filter berdasarkan admin jika ada
            if ($adminId) {
                $query->whereHas('owner.admins', function($q) use ($adminId) {
                    $q->where('id', $adminId);
                });
            }

            $layanan = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $layanan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
