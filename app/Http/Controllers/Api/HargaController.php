<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Harga;
use App\Models\DetailHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HargaController extends Controller
{
    public function index()
    {
        $hargas = Harga::with('detailHargas')->get();
        return response()->json(['status' => true,'data' => $hargas]);
    }

    public function show($id)
    {
        $harga = Harga::with('detailHargas')->find($id);
        if (!$harga) {
            return response()->json(['message' => 'Data harga tidak ditemukan'], 404);
        }
        return response()->json(['status' => true,'data' => $harga]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_laundry' => 'required|exists:laundry,id',
            'jenis_harga' => 'required|in:kiloan,satuan',
            'detail_hargas' => 'required|array',
            'detail_hargas.*.nama_item' => 'required|string',
            'detail_hargas.*.harga' => 'required|numeric',
            'detail_hargas.*.satuan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $harga = Harga::create([
            'id_laundry' => $request->id_laundry,
            'jenis_harga' => $request->jenis_harga
        ]);

        foreach ($request->detail_hargas as $detail) {
            DetailHarga::create([
                'harga_id' => $harga->id,
                'nama_item' => $detail['nama_item'],
                'harga' => $detail['harga'],
                'satuan' => $detail['satuan'] ?? null
            ]);
        }

        return response()->json([
            'message' => 'Data harga berhasil ditambahkan',
            'data' => $harga->load('detailHargas')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $harga = Harga::find($id);
        if (!$harga) {
            return response()->json(['message' => 'Data harga tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'jenis_harga' => 'required|in:kiloan,satuan',
            'detail_hargas' => 'required|array',
            'detail_hargas.*.nama_item' => 'required|string',
            'detail_hargas.*.harga' => 'required|numeric',
            'detail_hargas.*.satuan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $harga->update(['jenis_harga' => $request->jenis_harga]);
        $harga->detailHargas()->delete();

        foreach ($request->detail_hargas as $detail) {
            DetailHarga::create([
                'harga_id' => $harga->id,
                'nama_item' => $detail['nama_item'],
                'harga' => $detail['harga'],
                'satuan' => $detail['satuan'] ?? null
            ]);
        }

        return response()->json([
            'message' => 'Data harga berhasil diupdate',
            'data' => $harga->load('detailHargas')
        ]);
    }

    public function destroy($id)
    {
        $harga = Harga::find($id);
        if (!$harga) {
            return response()->json(['message' => 'Data harga tidak ditemukan'], 404);
        }

        $harga->delete();
        return response()->json(['message' => 'Data harga berhasil dihapus']);
    }

    public function getHargaByLaundry($laundryId)
    {
        $hargas = Harga::with('detailHargas')
            ->where('id_laundry', $laundryId)
            ->get();

        if ($hargas->isEmpty()) {
            return response()->json(['message' => 'Data harga tidak ditemukan untuk laundry ini'], 404);
        }

        return response()->json(['data' => $hargas]);
    }
}
