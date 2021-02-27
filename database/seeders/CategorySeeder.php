<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Partner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'name' => Str::random(4)
        ]);

        DB::table('categories')->insert([
            'name' => Str::random(4),
            'parent_id' => Category::where('parent_id', null)->first()->id
        ]);

        DB::table('partner_categories')->insert([
            'category_id' => Category::where('parent_id', '<>', null)->first()->id,
            'partner_id' => Partner::where('id', '<>', null)->first()->id
        ]);
    }
}
