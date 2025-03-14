<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index()
    {
        return response()->json(Tagihan::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_laundry' => 'required|exists:laundry,id',
            'id_pesanan' => 'required|exists:pesanan,id',
            'total_tagihan' => 'required|numeric',
        ]);

        $tagihan = Tagihan::create($request->all());

        return response()->json($tagihan, 201);
    }

    public function show($id)
    {
        return response()->json(Tagihan::findOrFail($id), 200);
    }

    public function update(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->update($request->all());

        return response()->json($tagihan, 200);
    }

    public function destroy($id)
    {
        Tagihan::destroy($id);
        return response()->json(['message' => 'Tagihan deleted'], 200);
    }
}
