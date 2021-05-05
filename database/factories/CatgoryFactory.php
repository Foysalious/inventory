<?php namespace Database\Factories;


use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CatgoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'parent_id'=>null,
            'thumb' =>$this->faker->imageUrl(),
            'banner' => $this->faker->imageUrl(),
            'app_thumb' => $this->faker->imageUrl(),
            'app_banner' => $this->faker->imageUrl(),
            'is_published'=>1,
            'is_published_for_sheba'=>1

        ];
    }
}
