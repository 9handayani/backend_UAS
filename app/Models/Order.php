<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',  // Tambahkan ini
        'phone_number',   // Tambahkan ini
        'address',        // Tambahkan ini
        'book_title',
        'total_amount',   // Sesuaikan dengan migration (total_amount)
        'payment_method', // Tambahkan ini
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}