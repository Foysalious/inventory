<?php namespace Database\Factories;


use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
{

    protected $model = Partner::class;
    /**
     * @inheritDoc
     */
    public function definition()
    {
        return[
            'sub_domain' => $this->faker->slug,
            'vat_percentage'=>'0.0'
        ];



    }
}
