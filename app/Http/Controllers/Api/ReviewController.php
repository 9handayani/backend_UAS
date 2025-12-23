<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user'    => 'required|string|max:255',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        // 2. Simpan ke database
        $review = Review::create($validated);

        // 3. Kembalikan respon sukses ke Next.js
        return response()->json($review, 201);
    }
}