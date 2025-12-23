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
    Schema::create('books', function (Blueprint $table) {
        $table->id();
        // Menghubungkan ke tabel categories
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->string('author');
        $table->decimal('price', 12, 2); // Contoh: 58500.00
        $table->integer('stock');
        $table->text('description')->nullable();
        $table->string('image'); // Menyimpan path gambar
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
