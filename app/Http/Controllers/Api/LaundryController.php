<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laundry;
use Illuminate\Http\Request;

class LaundryController extends Controller
{
    public function index()
    {
        return response()->json(Laundry::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'nomor' => 'required|string',
            'img' => 'required|string',
            'rating' => 'required|numeric|between:0,5',
            'jasa' => 'required|string',
        ]);

        $laundry = Laundry::create($request->all());

        return response()->json($laundry, 201);
    }

    public function show($id)
    {
        return response()->json(Laundry::findOrFail($id), 200);
    }

    public function update(Request $request, $id)
    {
        $laundry = Laundry::findOrFail($id);
        $laundry->update($request->all());

        return response()->json($laundry, 200);
    }

    public function destroy($id)
    {
        Laundry::destroy($id);
        return response()->json(['message' => 'Laundry deleted'], 200);
    }
}
