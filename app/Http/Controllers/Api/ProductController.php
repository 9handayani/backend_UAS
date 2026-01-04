<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    /**
     * Digunakan oleh Admin & User: Menampilkan semua produk
     */
    public function index(Request $request)
    {
        $query = Book::with('category')->latest();

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category != 'Semua') {
            $categoryName = $request->category;
            $query->whereHas('category', function($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ], 200);
    }

    /**
     * KHUSUS SEO: Mencari buku berdasarkan SLUG (Untuk User/Frontend)
     */
    public function showBySlug($slug)
    {
        try {
            $book = Book::with('category')->where('slug', $slug)->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => $book
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan.',
                'slug' => $slug
            ], 404);
        }
    }

    /**
     * KHUSUS ADMIN: Mencari buku berdasarkan ID (Untuk Fitur Edit)
     * Ini yang memperbaiki error "undefined method show()"
     */
    public function show($id)
    {
        try {
            $book = Book::with('category')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $book
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Buku dengan ID ' . $id . ' tidak ditemukan.'
            ], 404);
        }
    }

    /**
     * Tambah Produk Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0|max:100',
            'rating'      => 'nullable|numeric|min:0|max:5',
            'stock'       => 'required|integer|min:0',
            'image'       => 'required|string',
            'description' => 'required|string',
            'details'     => 'nullable', 
        ]);

        $validated['discount'] = $request->filled('discount') ? (int) $request->discount : 0;
        $validated['rating']   = $request->filled('rating')   ? (float) $request->rating : 0;
        $validated['slug']     = Str::slug($validated['title']);

        if ($request->has('details')) {
            $validated['details'] = is_array($request->details) ? $request->details : json_decode($request->details, true);
        }

        $book = Book::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dibuat',
            'data' => $book->load('category')
        ], 201);
    }

    /**
     * Update Produk (Untuk Admin)
     */
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'title'       => 'sometimes|required|string|max:255',
            'author'      => 'sometimes|required|string',
            'price'       => 'sometimes|required|numeric',
            'discount'    => 'nullable|numeric',
            'rating'      => 'nullable|numeric',
            'stock'       => 'sometimes|required|integer',
            'image'       => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'details'     => 'nullable'
        ]);

        if ($request->has('title')) {
            $validated['slug'] = Str::slug($request->title);
        }

        if ($request->has('details')) {
            $validated['details'] = is_array($request->details) ? $request->details : json_decode($request->details, true);
        }

        $book->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk Berhasil Diperbarui!',
            'data' => $book->refresh()->load('category')
        ], 200);
    }

    /**
     * Hapus Produk (Untuk Admin)
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus'
        ], 200);
    }
}