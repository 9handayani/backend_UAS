<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Book::with('category')->latest()->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0|max:100', // Tambahkan ini
            'rating'      => 'nullable|numeric|min:0|max:5',   // Tambahkan ini
            'stock'       => 'required|integer|min:0',
            'image'       => 'required|string',
            'description' => 'required|string',
            'details'     => 'nullable', 
        ]);

        // Paksa tipe data angka agar tidak masuk sebagai string
        $validated['discount'] = $request->filled('discount') ? (int) $request->discount : 0;
        $validated['rating']   = $request->filled('rating')   ? (float) $request->rating : 0;
        $validated['slug']     = Str::slug($validated['title']);

        // Handle JSON details
        if ($request->has('details')) {
            $validated['details'] = is_array($request->details) ? $request->details : json_decode($request->details, true);
        }

        $book = Book::create($validated);
        return response()->json($book->load('category'), 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        
        $validated = $request->validate([
            'category_id' => 'sometimes|integer|exists:categories,id',
            'title'       => 'sometimes|string|max:255',
            'author'      => 'sometimes|string|max:255',
            'price'       => 'sometimes|numeric',
            'discount'    => 'nullable|numeric', // Tambahkan ini
            'rating'      => 'nullable|numeric',   // Tambahkan ini
            'stock'       => 'sometimes|integer',
            'image'       => 'sometimes|string',
            'description' => 'sometimes|string',
            'details'     => 'nullable',
        ]);

        if ($request->has('title')) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Konversi tipe data untuk update
        if ($request->has('discount')) $validated['discount'] = (int) $request->discount;
        if ($request->has('rating'))   $validated['rating']   = (float) $request->rating;

        $book->update($validated);
        return response()->json($book->load('category'), 200);
    }

    public function show($id)
    {
        return Book::with('category')->findOrFail($id);
    }

    public function showBySlug($slug)
    {
        $book = Book::with('category')->where('slug', $slug)->firstOrFail();
        return response()->json($book, 200);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return response()->json(['message' => 'Produk berhasil dihapus'], 200);
    }
}