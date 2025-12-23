<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('books', function (Blueprint $table) {
        $table->string('slug')->unique()->after('title');
        $table->integer('discount')->default(0)->after('price');
        $table->float('rating')->default(0)->after('discount');
        $table->text('details')->nullable()->after('description'); // Simpan JSON untuk detail buku
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            //
        });
    }
};
