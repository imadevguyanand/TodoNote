<?php

namespace Database\Factories;

use App\Models\TodoNote;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoNoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TodoNote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->text(20)
        ];
    }
}
