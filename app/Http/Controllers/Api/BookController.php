<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        return response()->json(Book::with(['category'])->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'author'      => 'required|string',
            'price'       => 'required|numeric',
            'discount'    => 'nullable|numeric',
            'rating'      => 'nullable|numeric',
            'stock'       => 'required|integer',
            'description' => 'required|string',
            'details'     => 'nullable',
            'image'       => 'required' 
        ]);

        $validated['slug'] = Str::slug($request->title);
        
        // Memastikan tipe data benar untuk angka
        $validated['discount'] = $request->filled('discount') ? (int) $request->discount : 0;
        $validated['rating']   = $request->filled('rating')   ? (float) $request->rating : 0;

        if ($request->has('details')) {
            $validated['details'] = is_array($request->details) ? $request->details : json_decode($request->details, true);
        }

        $book = Book::create($validated);
        return response()->json(['message' => 'Buku berhasil ditambahkan!', 'data' => $book->load('category')], 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        // 1. Validasi
        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'title'       => 'sometimes|required|string|max:255',
            'author'      => 'sometimes|required|string',
            'price'       => 'sometimes|required|numeric',
            'discount'    => 'nullable|numeric',
            'rating'      => 'nullable|numeric',
            'stock'       => 'sometimes|required|integer',
            'description' => 'sometimes|required|string',
            'details'     => 'nullable',
            'image'       => 'sometimes|required|string'
        ]);

        // 2. Olah Slug
        if ($request->has('title')) {
            $validated['slug'] = Str::slug($request->title);
        }

        // 3. LOGIKA KRUSIAL: Konversi Tipe Data
        // Karena Next.js mengirim JSON, Laravel terkadang membaca angka sebagai string
        if ($request->has('discount')) {
            $validated['discount'] = (int) $request->discount;
        }
        if ($request->has('rating')) {
            $validated['rating'] = (double) $request->rating;
        }

        // 4. Update data
        $book->update($validated);

        return response()->json([
            'message' => 'Produk Berhasil Diperbarui!',
            'data' => $book->refresh()->load('category')
        ], 200);
    }

    public function show($id) {
        $book = Book::with(['category'])->find($id);
        if (!$book) return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        return response()->json($book);
    }

    public function destroy($id) {
        $book = Book::findOrFail($id);
        $book->delete();
        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}