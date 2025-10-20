<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    protected $model = User::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
//            'rating_id' => function () {
//                $rating = Rating::inRandomOrder()->first();
//                return $rating ? $rating->id : Rating::factory();
//            },
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'is_available' => $this->faker->boolean(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone_number' => $this->faker->phoneNumber(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Assign random role after user is created
            $roles = ['normal-user', 'driver'];
            $randomRole = $this->faker->randomElement($roles);
            $user->assignRole($randomRole);
        });
    }
}
