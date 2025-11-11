<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    protected $model = \Workbench\App\Models\Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
