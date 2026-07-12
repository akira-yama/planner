<?php

namespace Database\Factories;

use App\Models\AgentDoc;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgentDoc>
 */
class AgentDocFactory extends Factory
{
    protected $model = AgentDoc::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'content' => fake()->paragraphs(3, true),
            'version' => '1.0',
            'metadata' => null,
        ];
    }

    public function versioned(string $version = '2.0'): static
    {
        return $this->state(fn (array $attributes) => [
            'version' => $version,
        ]);
    }
}