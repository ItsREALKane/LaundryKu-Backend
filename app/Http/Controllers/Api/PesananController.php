<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        $pesanan = Pesanan::with('user:id,name,phone')->get();
        return response()->json($pesanan, 200);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_user' => 'required|exists:users,id',
                'id_laundry' => 'required|exists:laundry,id',
                'tanggal_pesanan' => 'required|date',
                'status' => 'required|string',
                'total_harga' => 'required|numeric',
                'alamat' => 'required|string',
                'waktu_ambil' => 'required',
                'catatan' => 'nullable|string',
                'info_pesanan' => 'nullable|string',
                'jenis_pembayaran' => 'nullable|in:sekali,langganan',
                'tgl_langganan_berakhir' => 'nullable|date|required_if:jenis_pembayaran,langganan',
            ]);

            $pesanan = Pesanan::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $pesanan
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error terjadi',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $pesanan = Pesanan::with('user:id,name,phone')->findOrFail($id);
        return response()->json($pesanan, 200);
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
