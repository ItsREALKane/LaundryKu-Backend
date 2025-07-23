<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Login admin using email and password
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin = Admin::with(['owner'])->where('email', $request->email)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email atau password salah'
                ], 401);
            }

            $token = $admin->createToken('admin_auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login admin berhasil',
                'token' => $token,
                'admin' => $admin,
                'admin_id' => $admin->id,
                'owner' => $admin->owner
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
     * Logout admin dan hapus token
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
     * Get current admin data
     */
    public function getAdmin(Request $request)
    {
        try {
            $admin = $request->user()->load(['owner']);
            return response()->json([
                'status' => true,
                'message' => 'Data admin berhasil dicandak',
                'data' => $admin
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
    public function index(Request $request)
    {
        try {
            $query = Admin::with(['owner']);
            
            // Filter by owner if id_owner parameter is provided
            if ($request->has('id_owner')) {
                $query->where('id_owner', $request->id_owner);
            }
            
            $admins = $query->get();
            
            return response()->json([
                'status' => true,
                'message' => 'List of admins',
                'data' => $admins
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
        // Log incoming request data for debugging
        Log::info('Admin creation request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'nomor' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
            'id_owner' => 'required|exists:owners,id',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'nomor' => $request->nomor,
            'status' => $request->status,
            'id_owner' => $request->id_owner,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin created successfully',
            'data' => $admin->load(['owner'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::with(['owner'])->find($id);
        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Admin details',
            'data' => $admin
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:admins,email,' . $id,
            'nomor' => 'sometimes|string',
            'status' => 'sometimes|in:aktif,nonaktif',
            'id_owner' => 'sometimes|exists:owners,id',
            'password' => 'sometimes|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin->update([
            'name' => $request->name ?? $admin->name,
            'email' => $request->email ?? $admin->email,
            'nomor' => $request->nomor ?? $admin->nomor,
            'status' => $request->status ?? $admin->status,
            'id_owner' => $request->id_owner ?? $admin->id_owner,
            'password' => $request->password ? Hash::make($request->password) : $admin->password
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin updated successfully',
            'data' => $admin
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found'
            ], 404);
        }

        $admin->delete();
        return response()->json([
            'status' => true,
            'message' => 'Admin deleted successfully'
        ], 200);
    }
}