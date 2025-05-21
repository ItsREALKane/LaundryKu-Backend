<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Login admin using name and password
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'password' => 'required|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin = Admin::with('laundry')->where('name', $request->name)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Name atanapi password lepat'
                ], 401);
            }

            // Generate token for admin (assuming you have Sanctum set up for Admin model too)
            $token = $admin->createToken('admin_auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login admin berhasil',
                'token' => $token,
                'admin' => $admin,
                'admin_id' => $admin->id,
                'laundry' => $admin->laundry
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
            $admin = $request->user()->load('laundry');
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
    public function index()
    {
        $admins = Admin::with('laundry')->get();
        return response()->json([
            'status' => true,
            'message' => 'List of admins',
            'data' => $admins
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:admins,name',
            'id_laundry' => 'required|exists:laundry,id',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Admin::create([
            'name' => $request->name,
            'id_laundry' => $request->id_laundry,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin created successfully',
            'data' => $admin
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::with('laundry')->find($id);
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
            'name' => 'sometimes|unique:admins,name,' . $id,
            'id_laundry' => 'sometimes|exists:laundry,id',
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
            'id_laundry' => $request->id_laundry ?? $admin->id_laundry,
            'password' => $request->password ? bcrypt($request->password) : $admin->password
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