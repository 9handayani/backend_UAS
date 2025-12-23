<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller {
    // 1. Mengambil semua data pesanan untuk Admin Panel
    public function index() {
        // Mengambil data terbaru agar pesanan baru muncul paling atas
        return response()->json(Order::latest()->get()); 
    }

    // 2. MENERIMA DATA DARI FORM CHECKOUT (Fungsi Baru)
    public function store(Request $request) {
        $request->validate([
            'user_id' => 'required',
            'customer_name' => 'required|string',
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'book_title' => 'required|string',
            'total_amount' => 'required|numeric',
            'payment_method' => 'required|string',
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'book_title' => $request->book_title,
            'total_amount' => $request->total_amount,
            'payment_method' => $request->payment_method,
            'status' => 'pending', // Status awal otomatis pending
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat',
            'data' => $order
        ], 201);
    }

    // 3. Mengubah status di Admin Panel
    public function updateStatus(Request $request, $id) {
        $order = Order::findOrFail($id);
        
        // Pastikan status yang dikirim valid sesuai enum di migration
        $order->status = $request->status; 
        $order->save();

        return response()->json([
            'message' => 'Status Updated',
            'data' => $order
        ]);
    }
}