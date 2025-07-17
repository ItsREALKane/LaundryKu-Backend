<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LaundryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Laundry::with(['kategori']);
            
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('rating')) {
                $query->where('rating', '>=', $request->rating);
            }

            if ($request->has('jasa')) {
                $query->where('jasa', 'like', '%' . $request->jasa . '%');
            }

            if ($request->has('pengantaran')) {
                $query->where('pengantaran', $request->pengantaran);
            }

            $laundry = $query->latest()->get();

            return response()->json([
                'status' => true,
                'message' => 'Data laundry berhasil diambil',
                'data' => $laundry
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
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'nomor' => 'required|string|max:15',
                'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'rating' => 'required|numeric|between:0,5',
                'jasa' => 'required|string|max:500',
                'pengantaran' => 'required|in:ya,tidak',
                'status' => 'required|in:buka,tutup,maintenance',
                'jam_buka' => 'required|date_format:H:i',
                'jam_tutup' => 'required|date_format:H:i|after:jam_buka',
                'deskripsi' => 'nullable|string|max:1000',
                'kategori_ids' => 'required|array',
                'kategori_ids.*' => 'exists:kategori,id'
            ]);

            $laundryData = $request->except(['img', 'kategori_ids']);

            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imageName = time() . '_' . str_replace(' ', '_', $request->nama) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/images/laundry', $imageName);
                $laundryData['img'] = 'images/laundry/' . $imageName;
            }

            $laundry = Laundry::create($laundryData);
            $laundry->kategori()->attach($request->kategori_ids);

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Laundry geus dijieun',
                'data' => $laundry->load('kategori')
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
            $laundry = Laundry::with(['kategori', 'admins:id,name,email'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Data laundry berhasil diambil',
                'data' => $laundry
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Waduh! Laundry teu kapanggih',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $laundry = Laundry::findOrFail($id);

            $validatedData = $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'alamat' => 'sometimes|required|string|max:500',
                'nomor' => 'sometimes|required|string|max:15',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'rating' => 'sometimes|required|numeric|between:0,5',
                'jasa' => 'sometimes|required|string|max:500',
                'pengantaran' => 'sometimes|required|in:ya,tidak',
                'status' => 'sometimes|required|in:buka,tutup,maintenance',
                'jam_buka' => 'sometimes|required|date_format:H:i',
                'jam_tutup' => 'sometimes|required|date_format:H:i|after:jam_buka',
                'deskripsi' => 'nullable|string|max:1000',
                'kategori_ids' => 'sometimes|required|array',
                'kategori_ids.*' => 'exists:kategori,id'
            ]);

            $laundryData = $request->except(['img', 'kategori_ids']);

            if ($request->hasFile('img')) {
                // Hapus gambar lama
                if ($laundry->img) {
                    Storage::delete('public/' . $laundry->img);
                }

                $image = $request->file('img');
                $imageName = time() . '_' . str_replace(' ', '_', $request->nama ?? $laundry->nama) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/images/laundry', $imageName);
                $laundryData['img'] = 'images/laundry/' . $imageName;
            }

            $laundry->update($laundryData);

            // Update kategori lamun aya
            if ($request->has('kategori_ids')) {
                $laundry->kategori()->sync($request->kategori_ids);
            }

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Laundry geus diupdate',
                'data' => $laundry->load('kategori')
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
            $laundry = Laundry::findOrFail($id);

            // Hapus gambar
            if ($laundry->img) {
                Storage::delete('public/' . $laundry->img);
            }

            // Hapus data laundry
            $laundry->kategori()->detach(); // Hapus relasi dengan kategori
            $laundry->delete();

            return response()->json([
                'status' => true,
                'message' => 'Mantap! Laundry geus dihapus'
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
