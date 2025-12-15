<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->randomElement([
                'متجر يقدم منتجات عالية الجودة ويلبي احتياجات العملاء بأفضل الأسعار.',
                'نحرص على توفير تشكيلة واسعة من المنتجات مع خدمة عملاء مميزة.',
                'نوفر أحدث الأجهزة الإلكترونية من أفضل الماركات العالمية.',
                'مجموعة مختارة بعناية من الملابس الرجالية والنسائية.',
                'نوفر تشكيلة متنوعة من المواد الغذائية الطازجة يوميًا.',
                'متجر يوفر احتياجاتك اليومية بسهولة وسرعة.',
                'نقدم لكم كل ما تحتاجونه من مواد غذائية ومنتجات منزلية في مكان واحد.'
            ]),
            'is_active' => $this->faker->boolean(90),
            'lat' => $this->faker->latitude(),
            'long' => $this->faker->longitude(),
            'address' => $this->faker->address(),
        ];
    }
}
