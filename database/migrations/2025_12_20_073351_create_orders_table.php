<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        // Jembatan ke tabel users (untuk mencatat siapa yang beli)
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // Data Pengiriman (diambil dari form Detail Pengiriman)
        $table->string('customer_name');  // Nama Lengkap
        $table->string('phone_number');   // Nomor HP
        $table->text('address');          // Alamat Lengkap
        
        // Detail Pesanan
        $table->string('book_title'); 
        $table->decimal('total_amount', 12, 2);
        $table->string('payment_method'); // Bank Transfer, E-Wallet, atau COD
        
        // Status Pesanan
        $table->enum('status', ['pending', 'paid', 'shipped', 'completed'])->default('pending');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
