<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Partner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partner = Partner::where('id', '>', 0)->first();
        $category = Category::whereHas('partners', function ($q) use ($partner) {
            $q->where('partners.id', $partner->id);
        })->where('parent_id', '<>', null)->first();
        for ($i = 0; $i < 10; $i++) {
            DB::table('products')->insert([
                'partner_id' => $partner->id,
                'category_id' => $category->id
            ]);
        }
    }
}
