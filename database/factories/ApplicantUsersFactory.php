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
            'image' => 'default.jpg',
            'firstname' => fake()->firstName(),
            'middlename' => fake()->lastName(),
            'lastname' => fake()->lastName(),
            'phone_no' => fake()->phoneNumber(),
            'tel_no' => '',
            'sex' => 'male',
            'birthday' => '04-18-2002',
            'civil_status' => 'single',
            'address' => fake()->address(),
            'province' => fake()->streetAddress(),
            'city' => fake()->city(),
            'resume' => 'resume.pdf',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ];
    }
}
