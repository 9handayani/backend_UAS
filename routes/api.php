<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- AUTH ROUTES ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- ORDER ROUTES ---
Route::get('/orders', [OrderController::class, 'index']); 
Route::post('/orders', [OrderController::class, 'store']); 
Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

// --- CATEGORY ROUTES ---
Route::get('/categories', [CategoryController::class, 'index']);

// --- BOOK & PRODUCT CRUD ROUTES ---

/**
 * 1. API RESOURCE
 * Menangani CRUD Admin (index, store, show, update, destroy)
 */
Route::apiResource('books', ProductController::class);

/** * 2. ROUTE SLUG UNTUK USER
 * Diarahkan ke ProductController karena fungsi showBySlug ada di sana.
 * URL ini yang dipanggil oleh Next.js: /api/books/details/{slug}
 */
Route::get('/books/details/{slug}', [ProductController::class, 'showBySlug']); 

// --- REVIEW ROUTES ---
Route::post('/reviews', [ReviewController::class, 'store']);