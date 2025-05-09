<?php

namespace App\Http\Controllers;

use App\Models\FavoriteLaundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteLaundryController extends Controller
{
    public function index()
    {
        $favorites = FavoriteLaundry::with('laundry')
            ->where('id_user', Auth::id())
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $favorites
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_laundry' => 'required|exists:laundry,id'
        ]);

        $exists = FavoriteLaundry::where('id_user', Auth::id())
            ->where('id_laundry', $request->id_laundry)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laundry ieu tos aya dina daptar favorit'
            ], 400);
        }

        $favorite = FavoriteLaundry::create([
            'id_user' => Auth::id(),
            'id_laundry' => $request->id_laundry
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laundry ditambahkeun kana daptar favorit',
            'data' => $favorite->load('laundry')
        ], 201);
    }

    public function destroy($id)
    {
        $favorite = FavoriteLaundry::where('id_user', Auth::id())
            ->where('id_laundry', $id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data teu kapanggih'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Laundry dihapus tina daptar favorit'
        ]);
    }
}
