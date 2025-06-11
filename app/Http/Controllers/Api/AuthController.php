<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * Login user sareng generate token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email atanapi password lepat'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;


            if (!$user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kasalahan dina data user'
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => $user,
                'user_id' => $user->id
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
     * Register user anyar
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'phone' => 'nullable|string|max:15'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone ?? null
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil',
                'user' => $user,
                'token' => $token
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
     * Update profil user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:15',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? $user->phone,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Profil berhasil diropéa',
                'user' => $user
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
     * Ganti password user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed|different:current_password'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password ayeuna teu cocog'
                ], 401);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password berhasil dirobah'
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
     * Meunangkeun data user anu keur login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dicandak',
                'data' => $request->user()
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
     * Logout user sareng hapus token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
     * Nampilkeun form login
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

}

