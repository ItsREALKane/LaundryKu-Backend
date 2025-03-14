<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        return response()->json(Pesanan::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_laundry' => 'required|exists:laundry,id',
            'tanggal_pesanan' => 'required|date',
            'status' => 'required|string',
            'total_harga' => 'required|numeric',
            'alamat' => 'required|string',
            'waktu_ambil' => 'required',
            'catatan' => 'nullable|string',
        ]);

        $pesanan = Pesanan::create($request->all());

        return response()->json($pesanan, 201);
    }

    public function show($id)
    {
        return response()->json(Pesanan::findOrFail($id), 200);
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->update($request->all());

        return response()->json($pesanan, 200);
    }

    public function destroy($id)
    {
        Pesanan::destroy($id);
        return response()->json(['message' => 'Pesanan deleted'], 200);
    }
}
