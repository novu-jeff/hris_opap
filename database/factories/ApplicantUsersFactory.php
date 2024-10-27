<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicantUsers>
 */
class ApplicantUsersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => null,
            'firstname' => fake()->firstName(),
            'middlename' => fake()->lastName(),
            'lastname' => fake()->lastName(),
            'phone_no' => 09364344500,
            'tel_no' => '',
            'sex' => 'male',
            'birthday' => '2024-10-20',
            'civil_status' => 'single',
            'address' => fake()->address(),
            'province' => fake()->streetAddress(),
            'city' => fake()->city(),
            'resume' => null,
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ];
    }
}
