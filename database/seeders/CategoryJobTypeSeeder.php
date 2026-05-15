<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\JobType;
use Illuminate\Support\Str;

class CategoryJobTypeSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Engineering', 'Design', 'Marketing', 'Product', 'Data Science', 'Sales'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat)],
                ['name' => $cat]
            );
        }

        $types = ['Full-time', 'Part-time', 'Remote', 'Kontrak'];
        foreach ($types as $type) {
            JobType::firstOrCreate(
                ['slug' => Str::slug($type)],
                ['name' => $type]
            );
        }
    }
}