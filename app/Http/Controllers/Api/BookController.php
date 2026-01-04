<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Menampilkan daftar buku dengan fitur Pencarian dan Filter Kategori
     */
    public function index(Request $request) // PERBAIKAN: Tambahkan Request $request
    {
        // 1. Inisialisasi query dengan relasi kategori
        $query = Book::with(['category']);

        // 2. LOGIKA PENCARIAN (Keyword 'q')
        // Ini yang membuat pencarian di Next.js berfungsi
        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%");
            });
        }

        // 3. LOGIKA FILTER KATEGORI (Slug)
        // Berguna untuk halaman kategori agar tidak menampilkan semua buku
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 4. Ambil data terbaru
        // Gunakan get() agar data yang dikirim berupa Array (sesuai kode Next.js kamu)
        $books = $query->latest()->get();

        return response()->json($books);
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

        if ($request->has('title')) {
            $validated['slug'] = Str::slug($request->title);
        }

        if ($request->has('discount')) {
            $validated['discount'] = (int) $request->discount;
        }
        if ($request->has('rating')) {
            $validated['rating'] = (double) $request->rating;
        }

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