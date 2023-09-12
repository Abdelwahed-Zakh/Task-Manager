<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::all();
        $status = $this->faker->randomElement(['pending','in progress','completed']);
        return [
           'title' => $this->faker->sentence(3),
           'description'=> $this->faker->sentence(10),
           'due_date' => $this->faker->dateTimeBetween('+1 month' ,'+2 months'),
           'user_id' => $users->random()->id,
           'status' => $status
        ];
    }
}
