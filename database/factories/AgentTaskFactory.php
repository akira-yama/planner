<?php

namespace Database\Factories;

use App\Enums\AgentTaskStatus;
use App\Models\AgentTask;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<AgentTask>
 */
class AgentTaskFactory extends Factory
{
    protected $model = AgentTask::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'assigned_to' => null,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => AgentTaskStatus::Pending->value,
            'due_at' => null,
            'completed_at' => null,
            'metadata' => null,
        ];
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $user->id,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AgentTaskStatus::Pending->value,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AgentTaskStatus::InProgress->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AgentTaskStatus::Completed->value,
            'completed_at' => now(),
        ]);
    }

    public function withDueDate(Carbon $date = null): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => $date ?? now()->addDays(7),
        ]);
    }
}