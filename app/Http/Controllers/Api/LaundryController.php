<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'required|numeric|between:0,5',
            'jasa' => 'required|string',
            'pengantaran' => 'required|string',
        ]);

        $laundryData = $request->except('img');

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images/laundry', $imageName);
            $laundryData['img'] = 'images/laundry/' . $imageName;
        }

        $laundry = Laundry::create($laundryData);

        return response()->json($laundry, 201);
    }

    public function show($id)
    {
        return response()->json(Laundry::findOrFail($id), 200);
    }

    public function update(Request $request, $id)
    {
        $laundry = Laundry::findOrFail($id);

        $request->validate([
            'nama' => 'nullable|string',
            'alamat' => 'nullable|string',
            'nomor' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'nullable|numeric|between:0,5',
            'jasa' => 'nullable|string',
            'pengantaran' => 'nullable|string',
        ]);

        $laundryData = $request->except('img');

        if ($request->hasFile('img')) {
            // Hapus gambar lama jika ada
            if ($laundry->img) {
                Storage::delete('public/' . $laundry->img);
            }

            $image = $request->file('img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images/laundry', $imageName);
            $laundryData['img'] = 'images/laundry/' . $imageName;
        }

        $laundry->update($laundryData);

        return response()->json($laundry, 200);
    }

    public function destroy($id)
    {
        Laundry::destroy($id);
        return response()->json(['message' => 'Laundry deleted'], 200);
    }
}
