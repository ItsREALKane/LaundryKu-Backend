<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Kategori::with('laundries');
            
            if ($request->has('jenis_kategori')) {
                $query->where('jenis_kategori', 'like', '%' . $request->jenis_kategori . '%');
            }

            $kategori = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Aya masalah, coba deui nya!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'jenis_kategori' => 'required|string|max:100|unique:kategori,jenis_kategori',
                'deskripsi' => 'nullable|string|max:500',
                'icon' => 'nullable|string|max:50'
            ]);

            $kategori = Kategori::create($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Kategori geus dijieun',
                'data' => $kategori
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
            $kategori = Kategori::with('laundries')
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil diambil',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Kategori teu kapanggih',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kategori = Kategori::findOrFail($id);

            $validatedData = $request->validate([
                'jenis_kategori' => 'sometimes|required|string|max:100|unique:kategori,jenis_kategori,' . $id,
                'deskripsi' => 'nullable|string|max:500',
                'icon' => 'nullable|string|max:50'
            ]);

            $kategori->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Kategori geus diupdate',
                'data' => $kategori
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
            $kategori = Kategori::findOrFail($id);
            
            // Cek heula aya laundry nu make kategori ieu teu
            if ($kategori->laundries()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Waduh! Kategori ieu masih dipake ku sababaraha laundry, teu bisa dihapus!'
                ], 422);
            }

            $kategori->delete();

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Kategori geus dihapus'
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