<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Novel', 'Komik', 'Pendidikan', 'Self Improvement','Musik', 'Komputer'];

        foreach ($categories as $item) {
            Category::create([
                'name' => $item,
                'slug' => Str::slug($item)
            ]);
        }
    }
}