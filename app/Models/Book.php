<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'title', 'slug', 'author', 'price', 
        'discount', 'rating', 'stock', 'description', 'details', 'image'
    ];

    // Hapus atau pastikan ini tidak memaksa nilai 0 setiap kali update/create
    // protected $attributes = [
    //     'discount' => 0,
    //     'rating' => 0,
    // ];

    protected $casts = [
        'details' => 'array',
        'price' => 'decimal:2', // Sesuaikan dengan decimal(12,2) di DB
        'discount' => 'integer',
        'rating' => 'double',    // Gunakan double sesuai struktur DB Anda
        'stock' => 'integer',
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class)->latest();
    }
}