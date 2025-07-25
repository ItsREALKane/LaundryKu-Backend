<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Pesanan;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OwnerController extends Controller
{
    /**
     * Register a new owner
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:owners',
                'email' => 'required|string|email|max:255|unique:owners',
                'password' => 'required|string|min:8|confirmed',
                'nama_laundry' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $owner = Owner::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nama_laundry' => $request->nama_laundry,
            ]);

            $token = $owner->createToken('owner-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Owner berhasil didaftarkan',
                'data' => [
                    'owner' => $owner,
                    'token' => $token
                ]
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
     * Login owner
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $owner = Owner::where('email', $request->email)->first();

            if (!$owner || !Hash::check($request->password, $owner->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email atanapi password lepat'
                ], 401);
            }

            $token = $owner->createToken('owner-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login owner berhasil',
                'token' => $token,
                'owner' => $owner,
                'owner_id' => $owner->id,
                'nama_laundry' => $owner->nama_laundry
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
     * Logout owner
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logout berhasil'
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
     * Get owner profile
     */
    public function profile(Request $request)
    {
        try {
            $owner = $request->user();
            $owner->load(['pesanan.user', 'pesanan.detailPesanan', 'pesanan.tagihan']);
            return response()->json([
                'status' => true,
                'message' => 'Data owner berhasil dicandak',
                'data' => $owner
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
     * Update owner profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $owner = $request->user();

            $validator = Validator::make($request->all(), [
                'username' => 'sometimes|string|max:255|unique:owners,username,' . $owner->id,
                'email' => 'sometimes|string|email|max:255|unique:owners,email,' . $owner->id,
                'nama_laundry' => 'sometimes|string|max:255',
                'password' => 'sometimes|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only(['username', 'email', 'nama_laundry']);
            
            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $owner->update($updateData);

            return response()->json([
                'status' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => $owner->fresh()
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
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $owners = Owner::with(['pesanan.user', 'pesanan.detailPesanan', 'pesanan.tagihan'])->get();
            return response()->json([
                'status' => true,
                'message' => 'Daftar owner berhasil diambil',
                'data' => $owners
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $owner = Owner::with(['pesanan.user', 'pesanan.detailPesanan', 'pesanan.tagihan'])->find($id);

            if (!$owner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Owner teu kapanggih'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Detail owner',
                'data' => $owner
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
                'username' => 'required|string|max:255|unique:owners',
                'email' => 'required|string|email|max:255|unique:owners',
                'password' => 'required|string|min:8',
                'nama_laundry' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $owner = Owner::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nama_laundry' => $request->nama_laundry,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Owner berhasil dibuat',
                'data' => $owner
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $owner = Owner::find($id);

            if (!$owner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Owner teu kapanggih'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'username' => 'sometimes|string|max:255|unique:owners,username,' . $id,
                'email' => 'sometimes|string|email|max:255|unique:owners,email,' . $id,
                'nama_laundry' => 'sometimes|string|max:255',
                'password' => 'sometimes|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only(['username', 'email', 'nama_laundry']);
            
            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $owner->update($updateData);

            return response()->json([
                'status' => true,
                'message' => 'Owner berhasil diperbarui',
                'data' => $owner->fresh()
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
    public function destroy(string $id)
    {
        try {
            $owner = Owner::find($id);

            if (!$owner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Owner teu kapanggih'
                ], 404);
            }

            $owner->delete();

            return response()->json([
                'status' => true,
                'message' => 'Owner berhasil dihapus'
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
     * Get pesanan data for owner dashboard
     */
    public function getPesanan(Request $request)
    {
        try {
            $owner = $request->user();
            
            // Gunakan relasi pesanan yang sudah didefinisikan di model Owner
            $query = $owner->pesanan()
                ->with([
                    'user:id,name,phone', 
                    'detailPesanan',
                    'tagihan'
                ]);
            
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('tanggal_mulai') && $request->has('tanggal_akhir')) {
                $query->whereBetween('tanggal_pesanan', [$request->tanggal_mulai, $request->tanggal_akhir]);
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
                'message' => 'Waduh! Aya masalah, coba deui nya!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get dashboard statistics for owner
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $owner = $request->user();
            
            // Hitung total pesanan menggunakan relasi pesanan
            $totalPesanan = $owner->pesanan()->count();
            
            // Hitung pendapatan total
            $totalPendapatan = $owner->pesanan()
                ->whereHas('tagihan', function($query) {
                    $query->where('status', 'lunas');
                })
                ->with('tagihan')
                ->get()
                ->sum(function($pesanan) {
                    return $pesanan->tagihan->total_harga;
                });
            
            // Hitung pesanan berdasarkan status
            $pesananByStatus = [
                'baru' => $owner->pesanan()->where('status', 'baru')->count(),
                'proses' => $owner->pesanan()->where('status', 'proses')->count(),
                'selesai' => $owner->pesanan()->where('status', 'selesai')->count(),
                'diambil' => $owner->pesanan()->where('status', 'diambil')->count(),
                'dibatalkan' => $owner->pesanan()->where('status', 'dibatalkan')->count(),
            ];
            
            // Pesanan terbaru (5 terakhir)
            $pesananTerbaru = $owner->pesanan()
                ->with(['user:id,name,phone', 'tagihan'])
                ->latest()
                ->take(5)
                ->get();
            
            return response()->json([
                'status' => true,
                'message' => 'Data statistik dashboard berhasil diambil',
                'data' => [
                    'total_pesanan' => $totalPesanan,
                    'total_pendapatan' => $totalPendapatan,
                    'pesanan_by_status' => $pesananByStatus,
                    'pesanan_terbaru' => $pesananTerbaru
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
}
