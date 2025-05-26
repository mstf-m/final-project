<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'سفر' => ['سفر جاده‌ای', 'کوهنوردی', 'تور شهری'],
            'ورزش' => ['فوتبال', 'والیبال', 'شنا'],
            'آموزش' => ['کارگاه آموزشی', 'سمینار', 'مطالعه گروهی']
        ];
        
        foreach ($categories as $parent => $children) {
            $parentCategory = Category::create(['name' => $parent]);
            
            foreach ($children as $child) {
                Category::create([
                    'name' => $child,
                    'parent_id' => $parentCategory->id
                ]);
            }
        }
    }
}