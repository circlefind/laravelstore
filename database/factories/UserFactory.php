<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Administrator',
            'email' => 'circlefind@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$12$q5yhMiGKKmKwAHMX4wmv8eMCoMctfjkNzPZccVUhtPK9VuysH7Z/W', // adminsise
            'remember_token' => Str::random(10),
        ];
    }
}
