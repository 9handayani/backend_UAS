<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Str;

class BookSeeder extends Seeder {
    public function run() {
        // 1. BUAT KATEGORI DENGAN SLUG
        $categories = ['Novel', 'Komik', 'Pendidikan'];
        $createdCategories = [];

        foreach ($categories as $catName) {
            $createdCategories[] = Category::updateOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName)] // Tambahkan ini agar tidak error 'Field slug doesn't have a default value'
            );
        }

        // 2. DATA BUKU
        $books = [
            ['title' => 'Mashle 12', 'author' => 'Hajime Komoto', 'price' => 31500],
            ['title' => 'UUD 1945', 'author' => 'Tim Miracle', 'price' => 28000],
            ['title' => 'Seporsi Mie Ayam', 'author' => 'Brian Khrisna', 'price' => 58500],
        ];

        foreach ($books as $item) {
            Book::create([
                'title' => $item['title'],
                'author' => $item['author'],
                'price' => $item['price'],
                'discount' => 20,
                'slug' => Str::slug($item['title']), // Slug untuk buku
                'image' => 'books/default.jpg', 
                'stock' => 50,
                'description' => 'Deskripsi buku ' . $item['title'],
                'category_id' => $createdCategories[0]->id // Masuk ke kategori pertama (Novel)
            ]);
        }
    }
}