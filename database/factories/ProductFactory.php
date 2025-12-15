<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id'=> Brand::inRandomOrder()->first()?->id ?? Brand::factory(),
            'store_id' => Store::inRandomOrder()->first()?->id ?? Store::factory(),
            'name' => $this->faker->randomElement([
                'ساعة يد رجالية',
                'حاسوب محمول',
                'قميص قطني',
                'علبة شوكولاتة',
                'سماعات بلوتوث',
                'زيت عطري',
            ]),
            'description' => $this->faker->randomElement([
                'منتج عالي الجودة مناسب للاستخدام اليومي.',
                'مصنوع من مواد ممتازة ويوفر أداءً ممتازًا.',
                'خيار مثالي لمن يبحث عن الجودة والسعر المناسب.',
                'مصمم بعناية ليناسب احتياجات جميع المستخدمين.',
            ]),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'stock' => $this->faker->numberBetween(0, 200),
        ];
    }
}
